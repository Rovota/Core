<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Basic;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Rule;

class EndsWithRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|ValidationAction
	{
		if (!is_string($value)) {
			return ValidationAction::NextRule;
		}

		if (!str_ends_with($value, $options[0])) {
			return new ErrorMessage($this->name, 'The value must end with :ending.', data: [
				'ending' => $options[0],
			]);
		}
		return ValidationAction::NextRule;
	}
}