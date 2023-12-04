<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\DateTime;

use DateTimeZone;
use Rovota\Core\Support\Arr;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Rule;

class TimezoneRule extends Rule
{
	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|ValidationAction
	{
		if (is_string($value) || $value instanceof DateTimeZone) {
			$value = $value instanceof DateTimeZone ? $value->getName() : $value;
			if (Arr::contains(timezone_identifiers_list(), $value)) {
				return ValidationAction::NextRule;
			}
		}

		return new ErrorMessage($this->name, 'The value must be a valid timezone.', data: []);
	}
}