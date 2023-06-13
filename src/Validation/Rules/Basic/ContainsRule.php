<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Basic;

use Rovota\Core\Support\Arr;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Support\Str;
use Rovota\Core\Validation\Rules\Rule;

class ContainsRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if (is_string($value) && Str::contains($value, $options) === false) {
			return new ErrorMessage($this->name, 'The value must contain all of the specified items.', data: [
				'items' => $options,
			]);
		}

		if (is_array($value) && Arr::contains($value, $options) === false) {
			return new ErrorMessage($this->name, 'The value must contain all of the specified items.', data: [
				'items' => $options,
			]);
		}

		return true;
	}
}