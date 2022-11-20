<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 *
 * Inspired by from the Laravel CollectionOld class.
 */

namespace Rovota\Core\Support;

use Closure;

final class CollectionOld
{

	/**
	 * Combines the values of the current collection (as keys) with the values provided.
	 */
	public function combine(mixed $values): CollectionOld
	{
		return new CollectionOld(ArrOld::combine($this->items, $values));
	}

	/**
	 * Returns the values in the current collection that are not present in the given collection.
	 */
	public function diff(mixed $items): CollectionOld
	{
		return new CollectionOld(ArrOld::diff($this->items, $items));
	}

	/**
	 * Returns the key and value pairs in the current collection that are not present in the given collection.
	 */
	public function diffAssoc(mixed $items): CollectionOld
	{
		return new CollectionOld(ArrOld::diffAssoc($this->items, $items));
	}

	/**
	 * Returns the keys with their values in the current collection that are not present in the given collection.
	 */
	public function diffKeys(mixed $items): CollectionOld
	{
		return new CollectionOld(ArrOld::diffKeys($this->items, $items));
	}

	/**
	 * Returns all duplicate values in the collection. Optionally, a key or callback can be provided.
	 */
	public function duplicates(callable|string|null $callback = null): CollectionOld
	{
		$items = $this->map(value_retriever($callback));

		$counters = [];
		$duplicates = [];

		foreach ($items as $key => $value) {
			if (isset($counters[$value]) === false) {
				$counters[$value] = 1;
				continue;
			}
			$duplicates[$key] = $value;
		}

		return new CollectionOld($duplicates);
	}

	/**
	 * Iterates over all items in the collection and passes the item to the given callback. Stops iterating when `false` is returned, or the end of the collection is reached.
	 */
	public function each(callable $callback): CollectionOld
	{
		foreach ($this->items as $key => $value) {
			if ($callback($value, $key) === false) {
				break;
			}
		}

		return $this;
	}

	/**
	 * Returns `true` when all items pass a given truth test using the given closure.
	 */
	public function every(callable $callback): bool
	{
		foreach ($this->items as $key => $value) {
			if ($callback($value, $key) === false) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Creates a new collection with each item only keeping the fields specified.
	 */
	public function fields(array $fields): CollectionOld
	{
		$result = [];

		foreach ($this->items as $key => $item) {
			$new = [];
			foreach ($fields as $name) {
				if (is_array($item)) {
					$new[$name] = $item[$name] ?? null;
				}
				if (is_object($item)) {
					$new[$name] = $item->{$name} ?? null;
				}
			}
			$result[$key] = $new;
		}

		return new CollectionOld($result);
	}

	/**
	 * Swaps the keys with their corresponding values.
	 */
	public function flip(): CollectionOld
	{
		return new CollectionOld(array_flip($this->items));
	}

	/**
	 * Removes all values from the collection that are not present in the given collection.
	 */
	public function intersect(mixed $items): CollectionOld
	{
		return new CollectionOld(array_intersect($this->items, convert_to_array($items)));
	}

	/**
	 * Removes all keys from the collection that are not present in the given collection.
	 */
	public function intersectByKeys(mixed $items): CollectionOld
	{
		return new CollectionOld(array_intersect_key($this->items, convert_to_array($items)));
	}

	/**
	 * Keys the collection using the given key, or the result of the callback.
	 */
	public function keyBy(callable|string $key_by): CollectionOld
	{
		$key_by = value_retriever($key_by);
		$results = [];

		foreach ($this->items as $key => $item) {
			$results[(string) $key_by($item, $key)] = $item;
		}

		return new CollectionOld($results);
	}

	/**
	 * Allows modifying the collection using a closure.
	 */
	public function modify(Closure $closure): CollectionOld
	{
		$closure($this);
		return $this;
	}

	/**
	 * Passes the collection to the callback and returns the result.
	 */
	public function pipe(callable $callback): mixed
	{
		return $callback($this);
	}

	/**
	 * Returns `x` random items from the collection. Defaults to `1`.
	 */
	public function random(int $items = 1): mixed
	{
		return ArrOld::random($this->items, $items);
	}

	/**
	 * Returns the result of the maximum value minus the minimum value.
	 */
	public function range(string|null $key = null): int|float
	{
		$values = $key !== null ? $this->pluck($key)->all() : $this->items;
		return max($values) - min($values);
	}

	/**
	 * Replaces the items in the current collection with the items in the given collection.
	 */
	public function replace(mixed $items): CollectionOld
	{
		return new CollectionOld(ArrOld::replace($this->items, $items));
	}

	/**
	 * Similar to `replace()`, but applies the same process to inner values.
	 */
	public function replaceRecursive(mixed $items): CollectionOld
	{
		return new CollectionOld(ArrOld::replaceRecursive($this->items, $items));
	}

	/**
	 * Remove existing keys and replace them with consecutive keys starting from 0.
	 */
	public function resetKeys(): CollectionOld
	{
		return new CollectionOld(array_values($this->items));
	}

	/**
	 * Skips over `x` items from the collection, and returns the remaining items.
	 */
	public function skip(int $count): CollectionOld
	{
		if ($this->count() <= $count) {
			return new CollectionOld();
		}

		$iterations = 0;
		$items = $this->items;

		foreach ($this->items as $key => $value) {
			if ($iterations === $count) {
				break;
			}
			unset($items[$key]);
			$iterations++;
		}
		return new CollectionOld($items);
	}

	/**
	 * Skips over the items from the collection until the closure returns `true`, and returns the remaining items.
	 */
	public function skipUntil(mixed $target): CollectionOld
	{
		if ($this->count() === 0) {
			return new CollectionOld();
		}

		$items = $this->items;
		foreach ($this->items as $key => $value) {
			if (($target instanceof Closure && $target($value, $key)) || $target === $value) {
				break;
			}
			unset($items[$key]);
		}
		return new CollectionOld($items);
	}

	/**
	 * Skips over the items from the collection as long as the closure returns `true`, and returns the remaining items.
	 */
	public function skipWhile(Closure $closure): CollectionOld
	{
		if ($this->count() === 0) {
			return new CollectionOld();
		}

		$items = $this->items;
		foreach ($this->items as $key => $value) {
			if ($closure($value, $key)) {
				unset($items[$key]);
				continue;
			}
			break;
		}
		return new CollectionOld($items);
	}

	/**
	 * Returns a slice of the collection starting at the given index, with a maximum number of items if defined.
	 */
	public function slice(int $offset, int|null $length = null, bool $preserve_keys = true): CollectionOld
	{
		return new CollectionOld(array_slice($this->items, $offset, $length, $preserve_keys));
	}

	/**
	 * Returns `x` number of items from the original collection.
	 */
	public function take(int $count): CollectionOld
	{
		if ($count < 0) {
			return $this->slice($count, abs($count));
		}

		return $this->slice(0, $count);
	}

	/**
	 * Returns all items after the specified value or when an item passes the truth test.
	 */
	public function takeFrom(mixed $closure): CollectionOld
	{
		$result = [];
		$found = false;
		foreach ($this->items as $key => $value) {
			if ($found === false) {
				if (($closure instanceof Closure && $closure($value, $key)) || $value === $closure) {
					$found = true;
				}
			} else {
				$result[$key] = $value;
			}
		}
		return new CollectionOld($result);
	}

	/**
	 * Returns all items up until the value has been found or an item passes a truth test.
	 */
	public function takeUntil(mixed $closure): CollectionOld
	{
		$result = [];
		foreach ($this->items as $key => $value) {
			if ($closure instanceof Closure && $closure($value, $key)) {
				break;
			} else if ($value === $closure) {
				break;
			} else {
				$result[$key] = $value;
			}
		}
		return new CollectionOld($result);
	}

	/**
	 * Returns all items until an item fails the given truth test.
	 */
	public function takeWhile(Closure $closure): CollectionOld
	{
		$result = [];
		foreach ($this->items as $key => $value) {
			if ($closure($value, $key) === false) {
				break;
			} else {
				$result[$key] = $value;
			}
		}
		return new CollectionOld($result);
	}

}