<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Advanced;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Rules\Rule;

class RegexRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if (preg_match($options[0], $value) === false) {
			return new ErrorMessage($this->name, 'The value does not match a required pattern.', data: [
				'pattern' => $options[0],
			]);
		}
		return true;
	}
}