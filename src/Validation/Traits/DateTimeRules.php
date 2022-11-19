<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Traits;

use DateTime;
use Rovota\Core\Support\ArrOld;

trait DateTimeRules
{

	protected function ruleAfter(string $field, mixed $data, mixed $target): bool
	{
		if (!moment($data)->isAfter($target)) {
			$this->addError($field, 'after');
			return false;
		}
		return true;
	}

	protected function ruleAfterOrEqual(string $field, mixed $data, mixed $target): bool
	{
		if (!moment($data)->isAfterOrEqual($target)) {
			$this->addError($field, 'after_or_equal');
			return false;
		}
		return true;
	}

	protected function ruleBefore(string $field, mixed $data, mixed $target): bool
	{
		if (!moment($data)->isBefore($target)) {
			$this->addError($field, 'before');
			return false;
		}
		return true;
	}

	protected function ruleBeforeOrEqual(string $field, mixed $data, mixed $target): bool
	{
		if (!moment($data)->isBeforeOrEqual($target)) {
			$this->addError($field, 'before_or_equal');
			return false;
		}
		return true;
	}

	protected function ruleBetweenDates(string $field, mixed $data, array $targets): bool
	{
		if (!moment($data)->isBetween($targets[0], $targets[1])) {
			$this->addError($field, 'between_dates');
			return false;
		}
		return true;
	}

	protected function ruleOutsideDates(string $field, mixed $data, array $targets): bool
	{
		if (!moment($data)->isOutside($targets[0], $targets[1])) {
			$this->addError($field, 'outside_dates');
			return false;
		}
		return true;
	}

	protected function ruleDateEquals(string $field, mixed $data, mixed $target): bool
	{
		if (!moment($data)->isEqual($target)) {
			$this->addError($field, 'date_equals');
			return false;
		}
		return true;
	}

	protected function ruleDateFormat(string $field, mixed $data, string|array $formats): bool
	{
		if (is_string($formats)) {
			$formats = [$formats];
		}

		$has_match = false;
		foreach ($formats as $format) {
			$date = DateTime::createFromFormat($format, $data);
			if ($date && $date->format($format) === $data) {
				$has_match = true;
			}
		}

		if (!$has_match) {
			$this->addError($field, 'date_format');
			return false;
		}
		return true;
	}

	protected function ruleTimezone(string $field, mixed $data): bool
	{
		if (!is_string($data)) {
			$this->addError($field, 'timezone');
			return false;
		}

		if (ArrOld::contains(timezone_identifiers_list(), $data) === false) {
			$this->addError($field, 'timezone');
			return false;
		}
		return true;
	}

}