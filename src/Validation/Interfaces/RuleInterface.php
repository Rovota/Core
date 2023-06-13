<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Interfaces;

use Rovota\Core\Support\ErrorMessage;

interface RuleInterface
{

	public function validate(string $attribute, mixed $value, array $options): true|ErrorMessage;

}