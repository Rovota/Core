<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Types;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Support\Moment;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class MomentRule extends Base
{

	public function __construct()
	{
		parent::__construct('moment');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if ($value instanceof Moment === false) {
			return new ErrorMessage($this->name, 'The value must be a valid Moment instance.');
		}
		return ValidationAction::NextRule;
	}

}