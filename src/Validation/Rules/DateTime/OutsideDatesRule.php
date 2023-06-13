<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\DateTime;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Rules\Rule;

class OutsideDatesRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if (!moment($value)->isOutside(...$options)) {
			return new ErrorMessage($this->name, 'The value must be outside the specified window.', data: [
				'start' => $options[0],
				'end' => $options[1],
			]);
		}
		return true;
	}
}