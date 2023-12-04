<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\DateTime;

use DateTime;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Rule;

class DateFormatRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|ValidationAction
	{
		foreach ($options as $format) {
			$date = DateTime::createFromFormat($format, $value);
			if ($date && $date->format($format) === $value) {
				return ValidationAction::NextRule;
			}
		}

		return new ErrorMessage($this->name, 'The value must follow a specified format.', data: [
			'formats' => $options,
		]);
	}
}