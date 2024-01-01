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

class OutsideDatesRule extends Base
{

	protected mixed $start = 'now';
	protected mixed $end = 'now';

	// -----------------

	public function __construct()
	{
		parent::__construct('outside_dates');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if (!moment($value)->isOutside($this->start, $this->end)) {
			return new ErrorMessage($this->name, 'The value must be outside the specified window.', data: [
				'start' => moment($this->start),
				'end' => moment($this->end),
			]);
		}

		return ValidationAction::NextRule;
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (count($options) === 2) {
			$this->start = $options[0];
			$this->end = $options[1];
		}

		return $this;
	}

}