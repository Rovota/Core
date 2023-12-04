<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Advanced;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Rule;

class NotRegexRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|ValidationAction
	{
		if (preg_match($options[0], $value)) {
			return new ErrorMessage($this->name, 'The value does not match an allowed pattern.', data: [
				'pattern' => $options[0],
			]);
		}
		return ValidationAction::NextRule;
	}
}