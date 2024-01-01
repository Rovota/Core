<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Types;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class IntegerRule extends Base
{

	public function __construct()
	{
		parent::__construct('integer');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if (!is_int($value)) {
			return new ErrorMessage($this->name, 'The value must be a valid integer.');
		}
		return ValidationAction::NextRule;
	}

}