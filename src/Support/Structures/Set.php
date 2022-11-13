<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */


namespace Rovota\Core\Support\Structures;

use ArrayIterator;
use Closure;
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

	public function get(mixed $key): mixed
	{
		return $this->offsetGet($key);
	}

	// -----------------

	public function add(mixed $values): void
	{
		foreach (convert_to_array($values) as $value) {
			$this->offsetSet($value, $value);
		}
	}

	// -----------------

	// public function contains(mixed $values): bool
	// {
	// 	if (is_array($values)) {
	// 		foreach ($values as $value) {
	// 			if ($this->contains($value) === false) {
	// 				return false;
	// 			}
	// 		}
	// 		return true;
	// 	}
	//
	// 	if ($values instanceof Closure) {
	// 		foreach ($this->storage as $key => $value) {
	// 			if ($values($key)) {
	// 				return true;
	// 			}
	// 		}
	// 		return true;
	// 	}
	// 	return in_array($values, array_flip($this->storage), true);
	// }
	//
	// public function filter(callable $callback): static
	// {
	// 	$filtered = [];
	// 	foreach ($this->storage as $key => $value) {
	// 		if ($callback($key) === true) {
	// 			$filtered[$key] = $value;
	// 		}
	// 	}
	// 	return new static($filtered);
	// }
	//
	// public function first(): mixed
	// {
	// 	foreach ($this->storage as $key => $value) {
	// 		return $key;
	// 	}
	// 	return null;
	// }

	// -----------------

	/**
	 * @internal
	 */
	public function getIterator(): Traversable
	{
		return new ArrayIterator(array_keys($this->values));
	}

}