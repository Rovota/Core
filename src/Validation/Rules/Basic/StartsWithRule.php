<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Basic;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Support\Str;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class StartsWithRule extends Base
{

	protected string $target = '-';

	// -----------------

	public function __construct()
	{
		parent::__construct('starts_with');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if (!is_string($value)) {
			return ValidationAction::NextRule;
		}

		if (!str_starts_with($value, $this->target)) {
			return new ErrorMessage($this->name, 'The value must start with :target.', data: [
				'actual' => Str::take($value, Str::length($this->target)),
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