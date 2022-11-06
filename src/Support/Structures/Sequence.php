<?php

namespace Rovota\Core\Support\Structures;

use TypeError;

class Sequence extends Collection
{

	public function __construct(mixed $items = [])
	{
		parent::__construct(array_values(convert_to_array($items)));
	}

	// -----------------

	// -----------------

	// -----------------

	public function offsetExists(mixed $offset): bool
	{
		if (is_int($offset) === false) {
			throw new TypeError('Only integers are allowed to be used as keys within a Sequence.');
		}
		return isset($this->values[$offset]);
	}

	public function offsetGet(mixed $offset): mixed
	{
		if (is_int($offset) === false) {
			throw new TypeError('Only integers are allowed to be used as keys within a Sequence.');
		}
		return $this->values[$offset] ?? null;
	}

	public function offsetSet(mixed $offset, mixed $value): void
	{
		if (is_null($offset)) {
			$this->values[] = $value;
			$this->keys[] = array_key_last($this->values);
		} else {
			if (is_int($offset) === false) {
				throw new TypeError('Only integers are allowed to be used as keys within a Sequence.');
			}
			$this->values[$offset] = $value;
			if (in_array($offset, $this->keys) === false) {
				$this->keys[$offset] = $offset;
			}
		}
	}

	public function offsetUnset(mixed $offset): void
	{
		if (is_int($offset) === false) {
			throw new TypeError('Only integers are allowed to be used as keys within a Sequence.');
		}
		unset($this->values[$offset]);
		unset($this->keys[$offset]);

		$this->values = array_values($this->values);
		$this->keys = array_keys($this->values);
	}

}