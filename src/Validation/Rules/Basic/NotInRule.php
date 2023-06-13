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
		if (Arr::contains($options, $value)) {
			return new ErrorMessage($this->name, 'The value may not be one of the specified items.', data: [
				'items' => $options,
			]);
		}

		return true;
	}
}