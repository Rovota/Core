<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Advanced;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Interfaces\RuleContextInterface;
use Rovota\Core\Validation\Rules\Rule;

class EqualRule extends Rule implements RuleContextInterface
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|ValidationAction
	{
		if ($this->context->get($options[0]) !== $value) {
			return new ErrorMessage($this->name, 'The value must be equal to :target.', data: [
				'target' => $options[0],
			]);
		}
		return ValidationAction::NextRule;
	}
}