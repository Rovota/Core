<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Interfaces;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;

interface RuleInterface
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|ValidationAction;

}