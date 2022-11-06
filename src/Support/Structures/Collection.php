<?php

namespace Rovota\Core\Support\Structures;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Rovota\Core\Support\Interfaces\Arrayable;
use Traversable;

abstract class Collection implements ArrayAccess, IteratorAggregate, Countable, Arrayable, JsonSerializable
{

	protected array $values;
	protected array $keys;

	private int $key_object_count = 0;

	// -----------------

	public function __construct(mixed $items = [])
	{
		$this->values = convert_to_array($items);
		$this->keys = array_keys($this->values);
	}

	// -----------------

	public function clear(): void
	{
		$this->values = [];
		$this->keys = [];
	}

	public function copy(): static
	{
		return clone $this;
	}

	public function count(): int
	{
		return count($this->keys);
	}

	public function isEmpty(): bool
	{
		return empty($this->keys);
	}

	public function isNotEmpty(): bool
	{
		return empty($this->keys) === false;
	}

	// -----------------

	public function contains(mixed $values): bool
	{
		if (is_array($values)) {
			foreach ($values as $value) {
				if ($this->contains($value) === false) {
					return false;
				}
			}
			return true;
		}

		if ($values instanceof Closure) {
			foreach ($this->values as $key => $value) {
				if ($values($value, $this->keys[$key])) {
					return true;
				}
			}
			return true;
		}
		return in_array($values, $this->values, true);
	}

	public function filter(callable $callback): static
	{
		$filtered = [];
		foreach ($this->values as $key => $value) {
			if ($callback($value, $this->keys[$key]) === true) {
				$filtered[$key] = $value;
			}
		}
		return new static($filtered);
	}

	public function first(callable|null $callback = null): mixed
	{
		if (empty($this->values)) {
			return null;
		}
		if (is_null($callback)) {
			foreach ($this->values as $value) {
				return $value;
			}
		}
		foreach ($this->values as $key => $value) {
			if ($callback($value, $this->keys[$key])) {
				return $value;
			}
		}
		return null;
	}

	public function last(callable|null $callback = null): mixed
	{
		if (empty($this->values)) {
			return null;
		}
		if (is_null($callback)) {
			return end($this->values);
		}
		foreach (array_reverse($this->values, true) as $key => $value) {
			if ($callback($value, $this->keys[$key])) {
				return $value;
			}
		}
		return null;
	}

	public function reject(callable $callback): static
	{
		$accepted = [];
		foreach ($this->values as $key => $value) {
			if ($callback($value, $this->keys[$key]) === false) {
				$accepted[$key] = $value;
			}
		}
		return new static($accepted);
	}

	public function toArray(): array
	{
		if ($this->key_object_count === 0) {
			return array_combine($this->keys, $this->values);
		} else {
			return $this->values;
		}
	}

	public function toJson(): string
	{
		return json_encode_clean($this->toArray());
	}

	// -----------------

	/**
	 * @internal
	 */
	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->toArray());
	}

	/**
	 * @internal
	 */
	public function jsonSerialize(): array
	{
		return $this->toArray();
	}

	/**
	 * @internal
	 */
	public function offsetExists(mixed $offset): bool
	{
		if (is_object($offset)) {
			$offset = spl_object_hash($offset);
		}
		return isset($this->values[$offset]);
	}

	/**
	 * @internal
	 */
	public function offsetGet(mixed $offset): mixed
	{
		if (is_object($offset)) {
			$offset = spl_object_hash($offset);
		}
		return $this->values[$offset] ?? null;
	}

	/**
	 * @internal
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		if (is_null($offset)) {
			$this->values[] = $value;
			$this->keys[] = array_key_last($this->values);
		} else {
			if (is_object($offset)) {
				$this->key_object_count++;
				$hash = spl_object_hash($offset);
				$this->values[$hash] = $value;
				$this->keys[$hash] = $offset;
			} else {
				$this->values[$offset] = $value;
				if (in_array($offset, $this->keys) === false) {
					$this->keys[$offset] = $offset;
				}
			}
		}
	}

	/**
	 * @internal
	 */
	public function offsetUnset(mixed $offset): void
	{
		if (is_object($offset)) {
			$offset = spl_object_hash($offset);
			if (isset($this->values[$offset])) {
				$this->key_object_count--;
			}
		}
		unset($this->values[$offset]);
		unset($this->keys[$offset]);
	}

	// -----------------

	protected function retrieveKeyForValue(mixed $value): mixed
	{
		return $this->keys[array_search($value, $this->values)] ?? null;
	}

	protected function retrieveValueForKey(mixed $key): mixed
	{
		return $this->values[array_search($key, $this->keys)] ?? null;
	}

}