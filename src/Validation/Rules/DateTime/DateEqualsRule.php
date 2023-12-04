<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\DateTime;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Rule;

class DateEqualsRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|ValidationAction
	{
		if (!moment($value)->isEqual($options[0])) {
			return new ErrorMessage($this->name, 'The value must match the specified date.', data: [
				'target' => $options[0],
			]);
		}
		return ValidationAction::NextRule;
	}
}