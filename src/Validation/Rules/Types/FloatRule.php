<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Types;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class FloatRule extends Base
{

	public function __construct()
	{
		parent::__construct('float');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if (!is_float($value)) {
			return new ErrorMessage($this->name, 'The value must be a valid floating-point number.');
		}
		return ValidationAction::NextRule;
	}

}