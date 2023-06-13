<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Advanced;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Interfaces\RuleContextInterface;
use Rovota\Core\Validation\Rules\Rule;

class DifferentRule extends Rule implements RuleContextInterface
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if ($this->context->get([$options[0]]) === $value) {
			return new ErrorMessage($this->name, 'The value must be different than :target.', data: [
				'target' => $options[0],
			]);
		}
		return true;
	}
}