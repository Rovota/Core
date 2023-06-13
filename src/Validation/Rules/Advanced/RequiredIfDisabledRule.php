<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Advanced;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Rules\Rule;

class RequiredIfDisabledRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if ($this->context->bool($options[0]) === false && $value === null) {
			return new ErrorMessage($this->name, "A value is required when ':target' is disabled.", data: [
				'target' => $options[0],
			]);
		}
		return true;
	}
}