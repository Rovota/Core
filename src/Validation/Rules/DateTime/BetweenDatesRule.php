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

class BetweenDatesRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|ValidationAction
	{
		if (!moment($value)->isBetween(...$options)) {
			return new ErrorMessage($this->name, 'The value must be within the specified window.', data: [
				'start' => $options[0],
				'end' => $options[1],
			]);
		}
		return ValidationAction::NextRule;
	}
}