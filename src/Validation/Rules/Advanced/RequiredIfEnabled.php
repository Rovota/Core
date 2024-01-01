<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Advanced;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;
use Rovota\Core\Validation\Interfaces\ContextAware;

class RequiredIfEnabled extends Base implements ContextAware
{

	protected string $target = '-';

	// -----------------

	public function __construct()
	{
		parent::__construct('required_if_enabled');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if ($this->context->bool($this->target) && $value === null) {
			return new ErrorMessage($this->name, "A value is required when ':target' is enabled.", data: [
				'target' => $this->target,
			]);
		}

		if ($this->context->bool($this->target) === false && $value === null) {
			return ValidationAction::NextField;
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