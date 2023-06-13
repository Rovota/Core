<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Basic;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Rules\Rule;

class StartsWithRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if (!is_string($value)) {
			return true;
		}

		if (!str_starts_with($value, $options[0])) {
			return new ErrorMessage($this->name, 'The value must start with :ending.', data: [
				'ending' => $options[0],
			]);
		}
		return true;
	}
}