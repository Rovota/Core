<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use Dflydev\DotAccessData\Data;
use IteratorAggregate;
use JsonSerializable;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Structures\Map;
use Rovota\Core\Support\Interfaces\Arrayable;
use Rovota\Core\Support\Traits\TypeAccessors;
use Traversable;

final class RequestData implements ArrayAccess, IteratorAggregate, Countable, Arrayable, JsonSerializable
{
	use TypeAccessors;

	protected Data $items;

	// -----------------

	public function __construct(mixed $items = [])
	{
		$this->items = new Data($items);
	}

	// -----------------

	public function all(): array
	{
		return $this->toArray();
	}

	public function count(): int
	{
		return count($this->items->export());
	}

	public function except(array $keys): RequestData
	{
		$bucket = new RequestData($this->items->export());
		foreach ($keys as $key) {
			$bucket->remove($key);
		}
		return $bucket;
	}

	public function filled(mixed $key): bool
	{
		$keys = is_array($key) ? $key : [$key];
		foreach ($keys as $key) {
			if ($this->items->get($key) === null) {
				return false;
			}
		}
		return true;
	}

	public function flush(): RequestData
	{
		$this->items = new Data();
		return $this;
	}

	public function get(string $key, mixed $default = null): mixed
	{
		return $this->items->get($key, ($default instanceof Closure ? $default() : $default));
	}

	public function has(string|array $key): bool
	{
		$keys = is_array($key) ? $key : [$key];
		foreach ($keys as $key) {
			if ($this->items->has($key) === false) {
				return false;
			}
		}
		return true;
	}

	public function isEmpty(): bool
	{
		return empty($this->items->export());
	}

	public function missing(string|array $key): bool
	{
		$keys = is_array($key) ? $key : [$key];
		foreach ($keys as $key) {
			if ($this->items->has($key) === true) {
				return false;
			}
		}
		return true;
	}

	public function only(array $keys): RequestData
	{
		$bucket = new RequestData();
		foreach ($keys as $key) {
			$bucket->set($key, $this->items->get($key));
		}
		return $bucket;
	}

	public function remove(string|array $key): RequestData
	{
		if (is_array($key)) {
			foreach ($key as $offset) {
				$this->remove($offset);
			}
		} else {
			$this->items->remove($key);
		}
		return $this;
	}

	public function set(string|array $key, mixed $value = null): RequestData
	{
		if (is_array($key)) {
			foreach ($key as $offset => $item) {
				$this->set($offset, $item);
			}
		} else {
			$this->items->set($key, $value);
		}
		return $this;
	}

	public function toArray(): array
	{
		return $this->items->export();
	}

	public function toJson(): string
	{
		return json_encode_clean($this->items->export());
	}

	public function toBucket(): Bucket
	{
		return new Bucket($this->items->export());
	}

	public function toMap(): Map
	{
		return new Map($this->items->export());
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
		return $this->items->has($offset);
	}

	/**
	 * @internal
	 */
	public function offsetGet(mixed $offset): mixed
	{
		return $this->items->get($offset);
	}

	/**
	 * @internal
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		$this->items->set($offset, $value);
	}

	/**
	 * @internal
	 */
	public function offsetUnset(mixed $offset): void
	{
		$this->items->remove($offset);
	}
}