<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
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
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Support\Interfaces\Arrayable;
use Rovota\Core\Support\Str;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\Traits\Macroable;
use Rovota\Core\Support\Traits\TypeAccessors;
use Traversable;

class ErrorBucket implements ArrayAccess, IteratorAggregate, Countable, Arrayable, JsonSerializable
{
	use TypeAccessors, Conditionable, Macroable;

	protected Data $items;

	// -----------------

	public function __construct(mixed $items = [])
	{
		$this->items = new Data(convert_to_array($items));
	}

	// -----------------

	public function copy(): ErrorBucket
	{
		return clone $this;
	}

	public function count(mixed $key = null): int
	{
		return count($key !== null ? $this->get($key) : $this->items->export());
	}

	public function flush(): ErrorBucket
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
			if ($this->offsetExists($key) === false) {
				return false;
			}
		}
		return true;
	}

	public function import(mixed $data, bool $preserve = false): ErrorBucket
	{
		$mode = $preserve ? DataInterface::PRESERVE : DataInterface::MERGE;
		$this->items->import(convert_to_array($data), $mode);
		return $this;
	}

	public function isEmpty(): bool
	{
		return empty($this->items->export());
	}

	public function missing(mixed $key): bool
	{
		$keys = is_array($key) ? $key : [$key];
		foreach ($keys as $key) {
			if ($this->offsetExists($key) === true) {
				return false;
			}
		}
		return true;
	}

	public function only(array $keys): ErrorBucket
	{
		$bucket = new ErrorBucket();
		foreach ($keys as $key) {
			$bucket->set($key, $this->items->get($key));
		}
		return $bucket;
	}

	public function remove(mixed $key): ErrorBucket
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

	public function set(mixed $key, ErrorMessage|string $value): ErrorBucket
	{
		if (is_array($key)) {
			foreach ($key as $offset => $item) {
				$this->offsetSet($offset, $item);
			}
		} else {
			if (is_string($value)) {
				$value = new ErrorMessage(Str::afterLast($key, '.'), $value);
			}
			$this->offsetSet($key, $value);
		}
		return $this;
	}

	public function toArray(): array
	{
		return $this->items->export();
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
		if ($offset === null) {
			$items = $this->items->export();
			$items[] = $value;
			$this->items = new Data($items);
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