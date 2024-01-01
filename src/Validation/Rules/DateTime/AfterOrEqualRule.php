<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\DateTime;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class AfterOrEqualRule extends Base
{

	protected mixed $target = 'now';

	// -----------------

	public function __construct()
	{
		parent::__construct('after_or_equal');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if (!moment($value)->isAfterOrEqual($this->target)) {
			return new ErrorMessage($this->name, 'The value must be on or after the specified date.', data: [
				'target' => moment($this->target),
			]);
		}

		return ValidationAction::NextRule;
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (isset($options[0])) {
			$this->target = $options[0];
		}

		return $this;
	}

}