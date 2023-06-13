<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Basic;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Support\ValidationTools;
use Rovota\Core\Validation\Rules\Rule;

class GreaterThanOrEqualRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if (ValidationTools::getSize($value) < $options[0]) {
			return new ErrorMessage($this->name, 'The value must be equal or more than :target.', data: [
				'target' => $options[0],
			]);
		}
		return true;
	}
}