<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Advanced;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\FilterAction;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\FilterManager;
use Rovota\Core\Validation\Rules\Base;

class FilterRule extends Base
{

	protected array $filters = [];

	// -----------------

	public function __construct()
	{
		parent::__construct('filter');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if (!is_string($value)) {
			return ValidationAction::NextRule;
		}

		foreach ($this->filters as $filter_name) {
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

		return ValidationAction::NextRule;
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (empty($options) === false) {
			$this->filters = $options;
		}

		return $this;
	}

}