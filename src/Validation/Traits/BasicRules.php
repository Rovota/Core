<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Traits;

use BackedEnum;
use Rovota\Core\Helpers\Arr;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Text;

trait BasicRules
{

   protected function ruleArray(string $field, mixed $data, array $allowed = []): bool
	{
		if (!is_array($data)) {
			$this->addError($field, 'array');
			return false;
		}
		if (!empty($allowed)) {
			foreach ($data as $key => $value) {
				if (Arr::contains($allowed, $key) === false) {
					$this->addError($field, 'array');
					return false;
				}
			}
		}
		return true;
	}

	protected function ruleBool(string $field, mixed $data): bool
	{
		if (!is_bool($data)) {
			$this->addError($field, 'bool');
			return false;
		}
		return true;
	}

	protected function ruleString(string $field, mixed $data): bool
	{
		if (!is_string($data)) {
			$this->addError($field, 'string');
			return false;
		}
		return true;
	}

	protected function ruleFloat(string $field, mixed $data): bool
	{
		if (!is_float($data)) {
			$this->addError($field, 'float');
			return false;
		}
		return true;
	}

	protected function ruleNumeric(string $field, mixed $data): bool
	{
		if (!is_numeric($data)) {
			$this->addError($field, 'numeric');
			return false;
		}
		return true;
	}

	protected function ruleInt(string $field, mixed $data): bool
	{
		if (!is_int($data)) {
			$this->addError($field, 'int');
			return false;
		}
		return true;
	}

	protected function ruleEnum(string $field, mixed $data, BackedEnum|string $enum): bool
	{
		if ($enum instanceof BackedEnum) { $enum = $enum::class; }

		if ($data instanceof $enum) {
			return true;
		}

		if ($enum::tryFrom($data) === null) {
			$this->addError($field, 'enum');
			return false;
		}
		return true;
	}

	protected function ruleMoment(string $field, mixed $data): bool
	{
		if ($data instanceof Moment === false) {
			$this->addError($field, 'moment');
			return false;
		}
		return true;
	}

	// -----------------

	protected function ruleSize(string $field, mixed $data, int|float $target): bool
	{
		if ($this->getSize($data) !== $target) {
			$this->addError($field, 'size', [$target]);
			return false;
		}
		return true;
	}

	protected function ruleMax(string $field, mixed $data, int|float $limit): bool
	{
		if ($this->getSize($data) > $limit) {
			$this->addError($field, 'max', [$limit]);
			return false;
		}
		return true;
	}

	protected function ruleMin(string $field, mixed $data, int|float $limit): bool
	{
		if ($this->getSize($data) < $limit) {
			$this->addError($field, 'min', [$limit]);
			return false;
		}
		return true;
	}

	protected function ruleBetween(string $field, mixed $data, array $limits): bool
	{
		$data = $this->getSize($data);
		if ($data <= $limits[0] || $data >= $limits[1]) {
			$this->addError($field, 'between', $limits);
			return false;
		}
		return true;
	}

	protected function ruleRange(string $field, mixed $data, array $limits): bool
	{
		$data = $this->getSize($data);
		if ($data < $limits[0] || $data > $limits[1]) {
			$this->addError($field, 'range', $limits);
			return false;
		}
		return true;
	}

	protected function ruleGt(string $field, mixed $data, int|float $target): bool
	{
		if ($this->getSize($data) <= $target) {
			$this->addError($field, 'gt', [$target]);
			return false;
		}
		return true;
	}

	protected function ruleGte(string $field, mixed $data, int|float $target): bool
	{
		if ($this->getSize($data) < $target) {
			$this->addError($field, 'gte', [$target]);
			return false;
		}
		return true;
	}

	protected function ruleLt(string $field, mixed $data, int|float $target): bool
	{
		if ($this->getSize($data) >= $target) {
			$this->addError($field, 'lt', [$target]);
			return false;
		}
		return true;
	}

	protected function ruleLte(string $field, mixed $data, int|float $target): bool
	{
		if ($this->getSize($data) > $target) {
			$this->addError($field, 'lte', [$target]);
			return false;
		}
		return true;
	}

	// -----------------

	protected function ruleCase(string $field, mixed $data, string $case): bool
	{
		if (!is_string($data)) {
			return true;
		}
		$matches = match($case) {
			'camel' => Text::camel($data) === $data,
			'kebab' => Text::kebab($data) === $data,
			'lower' => Text::lower($data) === $data,
			'pascal' => Text::pascal($data) === $data,
			'snake' => Text::snake($data) === $data,
			'title' => Text::title($data) === $data,
			'upper' => Text::upper($data) === $data,
			default => true
		};
		if ($matches === false) {
			$this->addError($field, 'case');
			return false;
		}
		return true;
	}

	protected function ruleStartsWith(string $field, mixed $data, string $prefix): bool
	{
		if (!is_string($data)) {
			return true;
		}
		if (!str_starts_with($data, $prefix)) {
			$this->addError($field, 'starts_with');
			return false;
		}
		return true;
	}

	protected function ruleEndsWith(string $field, mixed $data, string $suffix): bool
	{
		if (!is_string($data)) {
			return true;
		}
		if (!str_ends_with($data, $suffix)) {
			$this->addError($field, 'ends_with');
			return false;
		}
		return true;
	}

	protected function ruleContains(string $field, mixed $data, string $needle): bool
	{
		if (is_string($data) && !Text::contains($data, $needle)) {
			$this->addError($field, 'contains', [$needle]);
			return false;
		}
		if (is_array($data) && Arr::contains($data, $needle) === false) {
			$this->addError($field, 'contains', [$needle]);
			return false;
		}
		return true;
	}

	protected function ruleContainsAny(string $field, mixed $data, array $needles): bool
	{
		if (is_string($data) && !Text::containsAny($data, $needles)) {
			$this->addError($field, 'contains_any', $needles);
			return false;
		}
		if (is_array($data) && Arr::containsAny($data, $needles) === false) {
			$this->addError($field, 'contains_any', $needles);
			return false;
		}
		return true;
	}

	protected function ruleContainsAll(string $field, mixed $data, array $needles): bool
	{
		if (is_string($data) && !Text::containsAll($data, $needles)) {
			$this->addError($field, 'contains_all', $needles);
			return false;
		}
		if (is_array($data) && Arr::contains($data, $needles) === false) {
			$this->addError($field, 'contains_all', $needles);
			return false;
		}
		return true;
	}

	protected function ruleContainsNone(string $field, mixed $data, array $needles): bool
	{
		if (is_string($data) && Text::containsAny($data, $needles)) {
			$this->addError($field, 'contains_none', $needles);
			return false;
		}
		if (is_array($data) && Arr::containsAny($data, $needles)) {
			$this->addError($field, 'contains_none', $needles);
			return false;
		}
		return true;
	}

	protected function ruleIn(string $field, mixed $data, array $needles): bool
	{
		if (Arr::contains($needles, $data) === false) {
			$this->addError($field, 'in', $needles);
			return false;
		}
		return true;
	}

	protected function ruleNotIn(string $field, mixed $data, array $needles): bool
	{
		if (Arr::contains($needles, $data)) {
			$this->addError($field, 'not_in', $needles);
			return false;
		}
		return true;
	}

}