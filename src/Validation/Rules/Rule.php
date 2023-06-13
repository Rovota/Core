<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules;

use Rovota\Core\Structures\Bucket;
use Rovota\Core\Validation\Interfaces\RuleInterface;

abstract class Rule implements RuleInterface
{

	protected string $name;

	protected Bucket $context;

	// -----------------

	public function __construct(string $name)
	{
		$this->name = $name;
		$this->context = new Bucket();
	}

	// -----------------

	public function setContext(array $data): void
	{
		$this->context->import($data);
	}

}