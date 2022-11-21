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
use Rovota\Core\Support\Helpers\Arr;
use Rovota\Core\Support\Interfaces\Arrayable;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\Traits\Macroable;
use Rovota\Core\Support\Traits\TypeAccessors;
use Stringable;
use Traversable;

class Bucket implements ArrayAccess, IteratorAggregate, Countable, Arrayable, JsonSerializable
{
	use TypeAccessors, Conditionable, Macroable;

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
		return Arr::average($field !== null ? $this->pluck($field)->toArray() : $this->items->export(), $round, $precision);
	}

	public function chunk(int $size): Bucket
	{
		$chunks = new Bucket();
		foreach (array_chunk($this->items->export(), $size, true) as $chunk) {
			$chunks->append(new Bucket($chunk));
		}
		return $chunks;
	}

	public function collapse(): Bucket
	{
		$collapsed = Arr::collapse($this->items->export());
		$this->items = new Data($collapsed);
		return $this;
	}

	public function concat(mixed $data): Bucket
	{
		foreach (convert_to_array($data) as $item) {
			$this->append($item);
		}
		return $this;
	}

	public function contains(mixed $value): bool
	{
		return Arr::contains($this->items->export(), $value);
	}

	public function containsAny(array $values): bool
	{
		return Arr::containsAny($this->items->export(), $values);
	}

	public function copy(): Bucket
	{
		return clone $this;
	}

	public function count(mixed $key = null): int
	{
		return count($key !== null ? $this->get($key) : $this->items->export());
	}

	public function countBy(callable|null $callback = null): Bucket
	{
		$counted = new Bucket();

		if ($callback === null) {
			$counted->import(array_count_values($this->items->export()));
		} else {
			foreach ($this->items->export() as $key => $value) {
				$counted->increment($callback($value, $key));
			}
		}

		return $counted;
	}

	public function decrement(mixed $key, int $step = 1): Bucket
	{
		$this->set($key, (int) $this->get($key, 0) - $step);
		return $this;
	}

	public function except(array $keys): Bucket
	{
		return $this->copy()->remove($keys);
	}

	public function filter(callable $callback): Bucket
	{
		return new Bucket(Arr::filter($this->items->export(), $callback));
	}

	public function find(mixed $value, bool $strict = true): string|int|bool
	{
		return Arr::search($this->items->export(), $value, $strict);
	}
	
	public function first(callable|null $callback = null, mixed $default = null): mixed
	{
		return Arr::first($this->items->export(), $callback, $default);
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

	public function groupBy(callable|string $group_by, bool $preserve_keys = false): Bucket
	{
		$group_by = value_retriever($group_by);
		$results = new Bucket();

		foreach ($this->items->export() as $key => $value) {

			$group_keys = $group_by($value, $key);

			if (is_array($group_keys) === false) {
				$group_keys = [$group_keys];
			}

			foreach ($group_keys as $group_key) {
				$group_key = match (true) {
					is_bool($group_key) => (int) $group_key,
					$group_key instanceof Stringable => (string) $group_key,
					default => $group_key,
				};

				if ($results->missing($group_key)) {
					$results[$group_key] = new Bucket();
				}

				$results[$group_key]->offsetSet($preserve_keys ? $key : null, $value);
			}
		}

		return $results;
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

	public function implode(string $value, string|null $glue = null): string
	{
		$first = $this->first();

		if (is_array($first) || (is_object($first) && !$first instanceof Stringable)) {
			return implode($glue ?? '', $this->pluck($value)->toArray());
		}

		return implode($value, $this->items->export());
	}

	public function import(mixed $data, bool $preserve = false): Bucket
	{
		$mode = $preserve ? DataInterface::PRESERVE : DataInterface::MERGE;
		$this->items->import(convert_to_array($data), $mode);
		return $this;
	}

	public function increment(mixed $key, int $step = 1): Bucket
	{
		$this->set($key, (int) $this->get($key, 0) + $step);
		return $this;
	}

	public function isEmpty(): bool
	{
		return empty($this->items->export());
	}

	public function join(string $glue, string $final_glue = ''): string
	{
		if ($final_glue === '') {
			return $this->implode($glue);
		}

		$count = $this->count();

		if ($count === 0) {
			return '';
		}

		if ($count === 1) {
			return (string) $this->first();
		}

		$bucket = new Bucket($this->items);
		$final_item = (string) $bucket->pop();

		return $bucket->implode($glue).$final_glue.$final_item;
	}

	public function keys(): Sequence
	{
		return new Sequence(array_keys($this->items->export()));
	}

	public function last(callable|null $callback = null, mixed $default = null): mixed
	{
		return Arr::last($this->items->export(), $callback, $default);
	}

	public function map(callable $callback): Bucket
	{
		return new Bucket(Arr::map($this->items->export(), $callback));
	}

	public function max(string|null $field = null, float|int|null $limit = null): float|int
	{
		return Arr::max($field !== null ? $this->pluck($field)->toArray() : $this->items->export(), $limit);
	}

	public function median(string|null $field = null): float|int|null
	{
		return Arr::median($field !== null ? $this->pluck($field)->toArray() : $this->items->export());
	}

	public function merge(mixed $with, bool $preserve = false): Bucket
	{
		return $this->copy()->import($with, $preserve);
	}

	public function min(string|null $field = null, float|int|null $limit = null): float|int
	{
		return Arr::min($field !== null ? $this->pluck($field)->toArray() : $this->items->export(), $limit);
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

	public function mode(string|null $field = null): array
	{
		return Arr::mode($field !== null ? $this->pluck($field)->toArray() : $this->items->export());
	}

	public function occurrences(string $value): int
	{
		return $this->countBy()[$value] ?? 0;
	}

	public function only(array $keys): Bucket
	{
		$bucket = new Bucket();
		foreach ($keys as $key) {
			$bucket->set($key, $this->items->get($key));
		}
		return $bucket;
	}

	public function partition(callable $callback): Bucket
	{
		$passed = new Bucket();
		$failed = new Bucket();

		foreach ($this->items as $key => $item) {
			if ($callback($item, $key)) {
				$passed->set($key, $item);
			} else {
				$failed->set($key, $item);
			}
		}

		return new Bucket([$passed, $failed]);
	}

	public function pluck(string $field, string|null $key = null): Bucket
	{
		return new Bucket(Arr::pluck($this->items->export(), $field, $key));
	}

	public function pop(int $count = 1): mixed
	{
		if ($count === 1) {
			$value = $this->last();
			$this->remove(array_search($value, $this->items->export()));
			return $value;
		}

		if ($this->isEmpty()) {
			return null;
		}

		$results = [];
		$item_count = $this->count();

		foreach (range(1, min($count, $item_count)) as $ignored) {
			$results[] = $this->last();
			$this->pop();
		}

		return new Sequence($results);
	}

	public function prepend(mixed $value): Bucket
	{
		$original = $this->items->export();
		array_unshift($original, $value);
		$this->items = new Data($original);
		return $this;
	}

	public function pull(mixed $key, mixed $default = null): mixed
	{
		$value = $this->offsetGet($key) ?? $default;
		$this->offsetUnset($key);
		return $value;
	}

	public function reduce(callable $callback, mixed $initial = null): mixed
	{
		return Arr::reduce($this->items->export(), $callback, $initial);
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

	public function reverse(): Bucket
	{
		$this->items = new Data(array_reverse($this->items->export(), true));
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

	public function shift(int $count = 1): mixed
	{
		if ($count === 1) {
			$value = $this->first();
			$this->remove(array_search($value, $this->items->export()));
			return $value;
		}

		if ($this->isEmpty()) {
			return null;
		}

		$results = [];
		$item_count = $this->count();

		foreach (range(1, min($count, $item_count)) as $ignored) {
			$results[] = $this->first();
			$this->shift();
		}

		return new Sequence($results);
	}

	public function shuffle(): Bucket
	{
		$this->items = new Data(Arr::shuffle($this->items->export()));
		return $this;
	}

	public function sort(callable|null $callback = null, bool $descending = false): Bucket
	{
		$this->items = new Data(Arr::sort($this->items->export(), $callback, $descending));
		return $this;
	}

	public function sortBy(mixed $callback, bool $descending = false): Bucket
	{
		$this->items = new Data(Arr::sortBy($this->items->export(), $callback, $descending));
		return $this;
	}

	public function sortKeys(bool $descending = false): Bucket
	{
		$this->items = new Data(Arr::sortKeys($this->items->export(), $descending));
		return $this;
	}

	public function sum(Closure|string|null $field = null): float|int
	{
		return Arr::sum($this->items->export(), $field);
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

	public function transform(callable $callback): Bucket
	{
		$this->items = new Data(Arr::map($this->items->export(), $callback));
		return $this;
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