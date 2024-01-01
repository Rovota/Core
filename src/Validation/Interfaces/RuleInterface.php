<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Interfaces;

use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;

interface RuleInterface
{

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction;

	// -----------------

	public function withOptions(array $options): static;

	// -----------------

	public function getName(): string;

}