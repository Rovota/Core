<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Kernel;

use BackedEnum;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Text;
use Rovota\Core\Support\Moment;
use Throwable;

final class Registry
{

	protected string $default_vendor = 'rovota';

	protected Bucket $options;

	// -----------------

	public function __construct()
	{
		$this->options = new Bucket();

		try {
			$options = RegistryOption::all();
			foreach ($options as $option) {
				$this->options->set($option->vendor.'.'.$option->name, $option);
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable, true);
		}
	}

	// -----------------

	public function array(string $name, array $default = []): array
	{
		$option = $this->get($name);
		return ($option !== null) ? explode(',', $option->value) : $default;
	}

	public function bool(string $name, bool $default = false): bool
	{
		$option = $this->get($name);
		return ($option !== null) ? filter_var($option->value, FILTER_VALIDATE_BOOLEAN) : $default;
	}

	public function float(string $name, float $default = 0.00): float
	{
		$option = $this->get($name);
		return ($option !== null) ? filter_var($option->value, FILTER_VALIDATE_FLOAT) : $default;
	}

	public function int(string $name, int $default = 0): int
	{
		$option = $this->get($name);
		return ($option !== null) ? filter_var($option->value, FILTER_VALIDATE_INT) : $default;
	}

	public function enum(string $name, BackedEnum|string $class, BackedEnum|null $default = null): BackedEnum|null
	{
		$option = $this->get($name);
		return ($option !== null) ? $class::TryFrom($option->value) : $default;
	}

	public function moment(string $name, Moment|null $default = null): Moment|null
	{
		try {
			$option = $this->get($name);
			return ($option !== null) ? new Moment($option->value) : $default;
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}

	public function string(string $name, string $default = ''): string
	{
		return $this->get($name)->value ?? $default;
	}

	// -----------------

	public function has(string $name): bool
	{
		return $this->options->has($this->getNameWithVendor($name));
	}

	public function get(string $name): RegistryOption|null
	{
		return $this->options->get($this->getNameWithVendor($name)) ?? null;
	}

	public function all(string|null $vendor = null): array
	{
		if ($vendor !== null) {
			return $this->options->get($vendor);
		} else {
			return $this->options->toArray();
		}
	}

	public function save(string $name, mixed $value): bool
	{
		$value = $this->getConvertedValue($value);

		if ($this->has($name)) {
			$option = $this->get($name);
			$option->value = $value;
			return $option->save();
		}

		[$vendor, $name] = explode('.', $this->getNameWithVendor($name));

		$option = new RegistryOption();
		$option->name = $name;
		$option->vendor = $vendor;
		$option->value = $value;

		if ($option->save()) {
			$this->options->set($vendor.'.'.$name, $option);
			return true;
		}

		return false;
	}

	public function delete(string $name, bool $permanent = false): bool
	{
		if ($this->has($name)) {
			$option = $this->get($name);
			if ($option->protected) {
				return false;
			}
			return $option->delete($permanent);
		}

		return true;
	}

	// -----------------

	protected function getNameWithVendor(string $name): string
	{
		if (!str_contains($name, '.')) {
			$name = $this->default_vendor.'.'.$name;
		}
		return $name;
	}

	protected function getConvertedValue(mixed $value): string|int
	{
		return match(true) {
			is_bool($value) => $value ? 1 : 0,
			is_float($value), $value instanceof Text => (string)$value,
			is_array($value) => implode(',', $value),
			$value instanceof Bucket => $value->join(','),
			$value instanceof BackedEnum => $value->value,
			default => $value,
		};
	}

}