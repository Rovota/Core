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

class CaseRule extends Base
{

	protected string $casing = '-';

	// -----------------

	public function __construct()
	{
		parent::__construct('case');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if (!is_string($value)) {
			return ValidationAction::NextRule;
		}

		$matches = match($this->casing) {
			'camel' => Str::camel($value) === $value,
			'kebab' => Str::kebab($value) === $value,
			'lower' => Str::lower($value) === $value,
			'pascal' => Str::pascal($value) === $value,
			'snake' => Str::snake($value) === $value,
			'title' => Str::title($value) === $value,
			'upper' => Str::upper($value) === $value,
			default => true
		};

		if ($matches === false) {
			return new ErrorMessage($this->name, 'The value must follow the specified casing.', data: [
				'casing' => $this->casing,
			]);
		}

		return ValidationAction::NextRule;
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (isset($options[0])) {
			$this->casing = $options[0];
		}

		return $this;
	}

}