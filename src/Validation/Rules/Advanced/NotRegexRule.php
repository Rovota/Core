<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Advanced;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class NotRegexRule extends Base
{

	protected string $pattern = 'now';

	// -----------------

	public function __construct()
	{
		parent::__construct('not_regex_rule');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if (preg_match($this->pattern, $value)) {
			return new ErrorMessage($this->name, 'The value does not match an allowed pattern.', data: [
				'pattern' => $this->pattern,
			]);
		}

		return ValidationAction::NextRule;
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (isset($options[0])) {
			$this->pattern = $options[0];
		}

		return $this;
	}

}