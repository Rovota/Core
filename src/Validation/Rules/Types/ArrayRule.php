<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Types;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class ArrayRule extends Base
{

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if (!is_array($value)) {
			return new ErrorMessage($this->name, 'The value must be a valid array.');
		}
		return ValidationAction::NextRule;
	}

}