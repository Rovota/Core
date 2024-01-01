<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Basic;

use Rovota\Core\Support\Arr;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class NotInRule extends Base
{

	protected array $items = [];

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if (count($this->items) === 1 && str_contains($this->items[0], '\\')) {
			if ($this->items[0]::tryFrom($value) !== null) {
				return new ErrorMessage($this->name, 'The value may not be one of the specified items.');
			}
		}

		if (count($this->items) > 1 && Arr::contains($this->items, $value)) {
			return new ErrorMessage($this->name, 'The value may not be one of the specified items.', data: [
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