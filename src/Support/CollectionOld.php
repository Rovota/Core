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
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\Traits\Macroable;
use Stringable;

final class CollectionOld
{
	use Macroable, Conditionable;

	// -----------------

	/**
	 * Split the collection into chunks of a given size.
	 */
	public function chunk(int $size): CollectionOld
	{
		$chunks = [];

		foreach (array_chunk($this->items, $size, true) as $chunk) {
			$chunks[] = new CollectionOld($chunk);
		}

		return new CollectionOld($chunks);
	}

	/**
	 * Combines the values of the current collection (as keys) with the values provided.
	 */
	public function combine(mixed $values): CollectionOld
	{
		return new CollectionOld(ArrOld::combine($this->items, $values));
	}

	/**
	 * Appends the values of a given array or collection onto the end of the current collection.
	 */
	public function concat(mixed $source): CollectionOld
	{
		$result = new CollectionOld($this);

		foreach (convert_to_array($source) as $item) {
			$result->push($item);
		}
		return $result;
	}

	/**
	 * Checks whether the provided value exists in the collection. Alternatively, you can use a closure to check whether an item exists matching a truth test.
	 */
	public function contains(mixed $value): bool
	{
		return ArrOld::contains($this->items, $value);
	}

	/**
	 * Checks whether all given values are present.
	 */
	public function containsAll(array $values): bool
	{
		return ArrOld::containsAll($this->items, $values);
	}

	/**
	 * Checks whether at least one of the given values is present.
	 */
	public function containsAny(array $values): bool
	{
		return ArrOld::containsAny($this->items, $values);
	}

	/**
	 * Checks whether none of the given values are present.
	 */
	public function containsNone(array $values): bool
	{
		return ArrOld::containsNone($this->items, $values);
	}

	/**
	 * Counts all occurrences of each value in the collection. Optionally, uses a callback to group occurrences.
	 */
	public function countBy(callable|null $callback = null): CollectionOld
	{
		if ($callback === null) {
			return new CollectionOld(array_count_values($this->items));
		}

		$counts = [];
		foreach ($this->items as $key => $value) {
			$group = $callback($value, $key);
			if (isset($counts[$group]) === false) {
				$counts[$group] = 0;
			}
			$counts[$group]++;
		}

		return new CollectionOld($counts);
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
	 * Return a new collection grouped by the given key, or a value returned from a callback.
	 */
	public function groupBy(callable|string $group_by, bool $preserve_keys = false): CollectionOld
	{
		// Inspired by the Laravel CollectionOld::groupBy() method.
		$group_by = value_retriever($group_by);

		$results = [];

		foreach ($this->items as $key => $value) {

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

				if (array_key_exists($group_key, $results) === false) {
					$results[$group_key] = new CollectionOld();
				}

				$results[$group_key]->offsetSet($preserve_keys ? $key : null, $value);
			}
		}

		return new CollectionOld($results);
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
	 * Iterates through the collection allowing modification of each item, returning a new collection with the result.
	 */
	public function map(callable $callback): CollectionOld
	{
		return new CollectionOld(ArrOld::map($this->items, $callback));
	}

	/**
	 * Returns the median of the collection or for a given key.
	 */
	public function median(string|null $key = null): float|int|null
	{
		return ArrOld::median($key !== null ? $this->pluck($key)->all() : $this->items);
	}

	/**
	 *	Returns the mode of the collection or given key.
	 */
	public function mode(string|null $key = null): array
	{
		return ArrOld::mode($key !== null ? $this->pluck($key)->all() : $this->items);
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
	 * Returns how many times a given value exists in the collection.
	 */
	public function occurrences(string $value): int
	{
		return $this->countBy()[$value] ?? 0;
	}

	/**
	 * Splits the collection into two collections, where one has items passing the truth test, and the other has items failing the truth test.
	 */
	public function partition(callable $callback): CollectionOld
	{
		$passed = [];
		$failed = [];

		foreach ($this->items as $key => $item) {
			if ($callback($item, $key)) {
				$passed[$key] = $item;
			} else {
				$failed[$key] = $item;
			}
		}

		return new CollectionOld([new CollectionOld($passed), new CollectionOld($failed)]);
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
	 * Reverses the order of the items in the collection while preserving the keys.
	 */
	public function reverse(): CollectionOld
	{
		return new CollectionOld(array_reverse($this->items, true));
	}

	/**
	 * Removes and returns the first item or `x` items from the collection. Defaults to `1`.
	 */
	public function shift(int $count = 1): mixed
	{
		if ($count === 1) {
			$value = array_shift($this->items);
			$this->keys = array_keys($this->items);
			return $value;
		}

		if ($this->isEmpty()) {
			return null;
		}

		$results = [];
		$item_count = $this->count();

		foreach (range(1, min($count, $item_count)) as $ignored) {
			$results[] = array_shift($this->items);
		}

		$this->keys = array_keys($this->items);
		return new CollectionOld($results);
	}

	/**
	 * Changes the order of the items in the collection to be random.
	 */
	public function shuffle(int|null $seed = null): CollectionOld
	{
		return new CollectionOld(ArrOld::shuffle($this->items, $seed));
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