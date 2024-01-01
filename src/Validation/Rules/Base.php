<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules;

use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Interfaces\RuleInterface;

abstract class Base implements RuleInterface
{

	public string $name;

	protected Bucket $context;

	// -----------------

	public function __construct(string $name)
	{
		$this->name = $name;
		$this->context = new Bucket();
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		return ValidationAction::NextRule;
	}

	// -----------------

	public function withContext(array $data): static
	{
		$this->context->import($data);
		return $this;
	}

	public function withOptions(array $options): static
	{
		return $this;
	}

	// -----------------

	public function getName(): string
	{
		return $this->name;
	}

	public function getContext(): Bucket
	{
		return $this->context;
	}

}