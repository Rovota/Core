<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Basic;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Support\ValidationTools;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class MaxRule extends Base
{

	protected float|int $size = 0;

	// -----------------

	public function __construct()
	{
		parent::__construct('max');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		$actual = ValidationTools::getSize($value);

		if ($actual > $this->size) {
			return new ErrorMessage($this->name, 'The value must be at most :target.', data: [
				'actual' => $actual,
				'target' => $this->size,
			]);
		}

		return ValidationAction::NextRule;
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (isset($options[0])) {
			$this->size = $options[0];
		}

		return $this;
	}

}