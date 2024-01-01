<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Advanced;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class EmailRule extends Base
{

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if (filter_var($value, FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE) === null) {
			return new ErrorMessage($this->name, 'The value must be a valid email address.');
		}

		return ValidationAction::NextRule;
	}
}