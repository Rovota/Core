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
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Rule;

class ContainsNoneRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|ValidationAction
	{
		if (is_string($value) && Str::containsAny($value, $options)) {
			return new ErrorMessage($this->name, 'The value may not contain any of the specified items.', data: [
				'items' => $options,
			]);
		}

		if (is_array($value) && Arr::containsAny($value, $options)) {
			return new ErrorMessage($this->name, 'The value may not contain any of the specified items.', data: [
				'items' => $options,
			]);
		}

		return ValidationAction::NextRule;
	}
}