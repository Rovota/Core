<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */


namespace Rovota\Core\Structures;

class Map extends Collection
{

	// -----------------
	// Shared

	public function get(mixed $key): mixed
	{
		return $this->offsetGet($key);
	}

	// -----------------
	// Structure Specific

	public function keys(): Sequence
	{
		return new Sequence(array_values($this->keys));
	}

	public function values(): Sequence
	{
		return new Sequence(array_values($this->values));
	}

}