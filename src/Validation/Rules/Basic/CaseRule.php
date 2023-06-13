<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Basic;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Support\Str;
use Rovota\Core\Validation\Rules\Rule;

class CaseRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if (!is_string($value)) {
			return true;
		}

		$matches = match($options[0]) {
			'camel' => Str::camel($value) === $value,
			'kebab' => Str::kebab($value) === $value,
			'lower' => Str::lower($value) === $value,
			'pascal' => Str::pascal($value) === $value,
			'snake' => Str::snake($value) === $value,
			'title' => Str::title($value) === $value,
			'upper' => Str::upper($value) === $value,
			default => true
		};

		if ($matches === false) {
			return new ErrorMessage($this->name, 'The value must follow the specified casing.', data: [
				'casing' => $options[0],
			]);
		}
		return true;
	}
}