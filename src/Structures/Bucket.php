<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Structures;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use Dflydev\DotAccessData\Data;
use Dflydev\DotAccessData\DataInterface;
use IteratorAggregate;
use JsonSerializable;
use Rovota\Core\Support\Helpers\Arr;
use Rovota\Core\Support\Interfaces\Arrayable;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\Traits\TypeAccessors;
use Traversable;

final class Bucket implements ArrayAccess, IteratorAggregate, Countable, Arrayable, JsonSerializable
{
	use TypeAccessors, Conditionable;

	protected Data $items;

	// -----------------

	public function __construct(mixed $items = [])
	{
		$this->items = new Data(convert_to_array($items));
	}

	// -----------------

	public function append(mixed $value): Bucket
	{
		if (is_array($value)) {
			foreach ($value as $item) {
				$this->offsetSet(null, $item);
			}
		} else {
			$this->offsetSet(null, $value);
		}
		return $this;
	}

	public function average(string|null $field = null, bool $round = false, int $precision = 0): float|int
	{
		return Arr::average($field !== null ? $this->pluck($field) : $this->items, $round, $precision);
	}

	public function copy(): Bucket
	{
		return clone $this;
	}

	public function count(mixed $key = null): int
	{
		return Arr::count($key !== null ? $this->get($key) : $this->items);
	}

	public function except(array $keys): Bucket
	{
		return $this->copy()->remove($keys);
	}

	public function flush(): Bucket
	{
		$this->items = new Data();
		return $this;
	}

	public function get(mixed $key, mixed $default = null): mixed
	{
		if (is_object($key)) {
			$key = spl_object_hash($key);
		}
		return $this->items->get($key, ($default instanceof Closure ? $default() : $default));
	}

	public function has(mixed $key): bool
	{
		$keys = is_array($key) ? $key : [$key];
		foreach ($keys as $key) {
			if ($this->items->has($key) === false) {
				return false;
			}
		}
		return true;
	}

	public function import(mixed $data, bool $preserve = false): Bucket
	{
		$mode = $preserve ? DataInterface::PRESERVE : DataInterface::MERGE;
		$this->items->import(convert_to_array($data), $mode);
		return $this;
	}

	public function isEmpty(): bool
	{
		return empty($this->items->export());
	}

	public function keys(): Sequence
	{
		return new Sequence(array_keys($this->items->export()));
	}

	public function max(string|null $field = null, float|int|null $limit = null): float|int
	{
		return Arr::max($field !== null ? $this->pluck($field) : $this->items, $limit);
	}

	public function merge(mixed $with, bool $preserve = false): Bucket
	{
		return $this->copy()->import($with, $preserve);
	}

	public function min(string|null $field = null, float|int|null $limit = null): float|int
	{
		return Arr::min($field !== null ? $this->pluck($field) : $this->items, $limit);
	}

	public function only(array $keys): Bucket
	{
		$bucket = new Bucket();
		foreach ($keys as $key) {
			$bucket->set($key, $this->items->get($key));
		}
		return $bucket;
	}

	public function pluck(string $field, string|null $key = null): Bucket
	{
		return new Bucket(Arr::pluck($this->items, $field, $key));
	}

	public function prepend(mixed $value): Bucket
	{
		$original = $this->items->export();
		array_unshift($original, $value);
		$this->items = new Data($original);
		return $this;
	}

	public function reduce(callable $callback, mixed $initial = null): mixed
	{
		return Arr::reduce($this->items, $callback, $initial);
	}

	public function remove(mixed $key): Bucket
	{
		if (is_array($key)) {
			foreach ($key as $offset) {
				$this->remove($offset);
			}
		} else {
			$this->offsetUnset($key);
		}
		return $this;
	}

	public function set(mixed $key, mixed $value = null): Bucket
	{
		if (is_array($key)) {
			foreach ($key as $offset => $item) {
				$this->offsetSet($offset, $item);
			}
		} else {
			$this->offsetSet($key, $value);
		}
		return $this;
	}

	public function sum(Closure|string|null $field = null): float|int
	{
		return Arr::sum($this->items, $field);
	}

	public function toArray(): array
	{
		return $this->items->export();
	}

	public function toJson(): string
	{
		return json_encode_clean($this->items->export());
	}

	public function toMap(): Map
	{
		return new Map($this->items->export());
	}

	public function toSequence(): Sequence
	{
		return new Sequence($this->items->export());
	}

	public function toSet(): Set
	{
		return new Set($this->items->export());
	}

	public function values(): Sequence
	{
		return new Sequence(array_values($this->items->export()));
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
		return $this->items->has($offset);
	}

	/**
	 * @internal
	 */
	public function offsetGet(mixed $offset): mixed
	{
		if (is_object($offset)) {
			$offset = spl_object_hash($offset);
		}
		return $this->items->get($offset);
	}

	/**
	 * @internal
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		if (is_null($offset)) {
			$this->items->set(null, $value);
		} else {
			if (is_object($offset)) {
				$offset = spl_object_hash($offset);
			}
			$this->items->set($offset, $value);
		}
	}

	/**
	 * @internal
	 */
	public function offsetUnset(mixed $offset): void
	{
		if (is_object($offset)) {
			$offset = spl_object_hash($offset);
		}
		$this->items->remove($offset);
	}

}