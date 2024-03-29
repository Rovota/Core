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

class DifferentRule extends Base implements ContextAware
{

	protected string $target = '-';

	// -----------------

	public function __construct()
	{
		parent::__construct('different');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if ($this->context->get($this->target) === $value) {
			return new ErrorMessage($this->name, 'The value must be different than :target.', data: [
				'target' => $this->target,
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