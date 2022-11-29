<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Structures;

use ArrayIterator;
use Traversable;

class Set extends Collection
{

	public function __construct(mixed $values = [])
	{
		parent::__construct();

		foreach (convert_to_array($values) as $value) {
			$this->offsetSet($value, $value);
		}
	}

	// -----------------
	// Shared

	public function get(mixed $key): mixed
	{
		return $this->offsetGet($key);
	}

	// -----------------
	// Structure Specific

	public function add(mixed $values): void
	{
		foreach (convert_to_array($values) as $value) {
			$this->offsetSet($value, $value);
		}
	}

	// -----------------

	/**
	 * @internal
	 */
	public function getIterator(): Traversable
	{
		return new ArrayIterator(array_keys($this->values));
	}

}