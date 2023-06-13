<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\DateTime;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Rules\Rule;

class AfterOrEqualRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if (!moment($value)->isAfterOrEqual($options[0])) {
			return new ErrorMessage($this->name, 'The value must be on or after the specified date.', data: [
				'target' => $options[0],
			]);
		}
		return true;
	}
}