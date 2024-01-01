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

class SizeRule extends Base
{

	protected float|int $target = 0;

	// -----------------

	public function __construct()
	{
		parent::__construct('size');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		$actual = ValidationTools::getSize($value);

		if ($actual !== $this->target) {
			return new ErrorMessage($this->name, 'The value must be of the required size.', data: [
				'actual' => $actual,
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