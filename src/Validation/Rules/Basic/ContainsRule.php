<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Basic;

use Rovota\Core\Support\Arr;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Support\Str;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class ContainsRule extends Base
{

	protected array $items = [];

	// -----------------

	public function __construct()
	{
		parent::__construct('contains');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if (is_string($value) && Str::contains($value, $this->items) === false) {
			return new ErrorMessage($this->name, 'The value must contain all of the specified items.', data: [
				'items' => $this->items,
			]);
		}

		if (is_array($value) && Arr::contains($value, $this->items) === false) {
			return new ErrorMessage($this->name, 'The value must contain all of the specified items.', data: [
				'items' => $this->items,
			]);
		}

		return ValidationAction::NextRule;
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (empty($options) === false) {
			$this->items = $options;
		}

		return $this;
	}

}