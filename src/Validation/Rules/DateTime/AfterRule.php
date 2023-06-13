<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\DateTime;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Rules\Rule;

class AfterRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if (!moment($value)->isAfter($options[0])) {
			return new ErrorMessage($this->name, 'The value must be after the specified date.', data: [
				'target' => $options[0],
			]);
		}
		return true;
	}
}