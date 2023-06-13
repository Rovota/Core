<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Basic;

use Rovota\Core\Support\Arr;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Rules\Rule;

class NotInRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if (count($options) === 1 && str_contains($options[0], '\\')) {
			if ($options[0]::tryFrom($value) !== null) {
				return new ErrorMessage($this->name, 'The value may not be one of the specified items.');
			}
		}

		if (count($options) > 1 && Arr::contains($options, $value)) {
			return new ErrorMessage($this->name, 'The value may not be one of the specified items.', data: [
				'items' => $options,
			]);
		}

		return true;
	}
}