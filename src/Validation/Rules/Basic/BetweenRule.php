<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Basic;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Support\ValidationTools;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Rule;

class BetweenRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|ValidationAction
	{
		[$min, $max] = $options;
		$size = ValidationTools::getSize($value);

		if ($size <= $min || $size >= $max) {
			return new ErrorMessage($this->name, 'The value must be between :min and :max.', data: [
				'min' => $options[0],
				'max' => $options[1],
			]);
		}
		return ValidationAction::NextRule;
	}
}