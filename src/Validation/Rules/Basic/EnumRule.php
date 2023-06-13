<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Basic;

use BackedEnum;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Rules\Rule;

class EnumRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if ($options[0] instanceof BackedEnum) {
			$options[0] = $options[0]::class;
		}

		if ($value instanceof $options[0] === false) {
			return new ErrorMessage($this->name, 'The value must be an enum of the specified type.');
		}
		return true;
	}
}