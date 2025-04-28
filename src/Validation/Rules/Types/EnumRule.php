<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Types;

use BackedEnum;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class EnumRule extends Base
{

	protected string $enum = BackedEnum::class;

	// -----------------

	public function __construct()
	{
		parent::__construct('enum');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if ($value instanceof $this->enum === false) {
			return new ErrorMessage($this->name, 'The value must be an enum of the specified type.', data: [
				'class' => $this->enum,
			]);
		}
		return ValidationAction::NextRule;
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (isset($options[0])) {
			$this->enum = $options[0] instanceof BackedEnum ? $options[0]::class : $options[0];
		}

		return $this;
	}

}