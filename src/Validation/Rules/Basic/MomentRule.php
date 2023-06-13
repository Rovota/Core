<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Basic;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Support\Moment;
use Rovota\Core\Validation\Rules\Rule;

class MomentRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if ($value instanceof Moment === false) {
			return new ErrorMessage($this->name, 'The value must be a valid Moment instance.');
		}
		return true;
	}
}