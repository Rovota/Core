<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Advanced;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\FilterAction;
use Rovota\Core\Validation\FilterManager;
use Rovota\Core\Validation\Rules\Rule;

class FilterRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if (!is_string($value)) {
			return true;
		}

		foreach ($options[0] as $filter_name) {
			if (FilterManager::has($filter_name)) {
				$filter = FilterManager::get($filter_name);

				if ($filter->action === FilterAction::Block && text($value)->lower()->containsAny($filter->values)) {
					return new ErrorMessage($this->name, 'The value may not contain any of the forbidden items.', data: [
						'value' => $value,
						'items' => $filter->values,
					]);
				}

				if ($filter->action === FilterAction::Allow && text($value)->lower()->containsNone($filter->values)) {
					return new ErrorMessage($this->name, 'The value must contain one of the required items.', data: [
						'value' => $value,
						'items' => $filter->values,
					]);
				}
			}
		}

		return true;
	}
}