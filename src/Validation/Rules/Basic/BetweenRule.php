<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Basic;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Support\ValidationTools;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class BetweenRule extends Base
{

	protected float|int $min = 0;
	protected float|int $max = 0;

	// -----------------

	public function __construct()
	{
		parent::__construct('between');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		$actual = ValidationTools::getSize($value);

		if ($actual <= $this->min || $actual >= $this->max) {
			return new ErrorMessage($this->name, 'The value must be between :min and :max.', data: [
				'actual' => $actual,
				'min' => $this->min,
				'max' => $this->max,
			]);
		}

		return ValidationAction::NextRule;
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (count($options) === 2) {
			$this->min = $options[0];
			$this->max = $options[1];
		}

		return $this;
	}

}