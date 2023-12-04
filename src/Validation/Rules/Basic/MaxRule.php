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

class MaxRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|ValidationAction
	{
		if (ValidationTools::getSize($value) > $options[0]) {
			return new ErrorMessage($this->name, 'The value must be at most :target.', data: [
				'target' => $options[0],
			]);
		}
		return ValidationAction::NextRule;
	}
}