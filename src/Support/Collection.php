<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 *
 * Inspired by from the Laravel Collection class.
 */

namespace Rovota\Core\Support;

use ArrayAccess;
use Closure;
use Countable;
use Iterator;
use JsonSerializable;
use Rovota\Core\Support\Interfaces\Arrayable;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\Traits\Macroable;
use Stringable;

final class Collection implements ArrayAccess, Iterator, Countable, Arrayable, JsonSerializable
{
	use Macroable, Conditionable;

	protected array $items = [];
	protected array $keys = [];

	protected int $position = 0;

	// -----------------

	public function __construct(mixed $items = [])
	{
		$this->items = convert_to_array($items);
		$this->keys = array_keys($this->items);
	}

	// -----------------

	public static function make(mixed $items): Collection
	{
		return new Collection($items);
	}

	// -----------------

	/**
	 * Adds a new value at the end of the collection, using a numeric key.
	 */
	public function add(mixed $item): Collection
	{
		$this->put(null, $item);
		return $this;
	}

	/**
	 * Returns the array used internally by the collection.
	 */
	public function all(): array
	{
		return $this->items;
	}

	/**
	 * Adds the value or values to the end of the collection. Optionally, you can provide a custom key as second parameter.
	 */
	public function append(mixed $value, string|int|null $key = null): Collection
	{
		$this->offsetSet($key, $value);
		return $this;
	}

	/**
	 * Retrieve the average value of a given key or the average of the collection.
	 */
	public function avg(string|null $key = null, bool $round = false): float|int|null
	{
		return Arr::avg($key !== null ? $this->pluck($key)->all() : $this->items, $round);
	}

	/**
	 * Split the collection into chunks of a given size.
	 */
	public function chunk(int $size): Collection
	{
		$chunks = [];

		foreach (array_chunk($this->items, $size, true) as $chunk) {
			$chunks[] = new Collection($chunk);
		}

		return new Collection($chunks);
	}

	/**
	 * Collapse a collection of arrays into a single, flat collection.
	 */
	public function collapse(): Collection
	{
		return new Collection(Arr::collapse($this->items));
	}

	/**
	 * Create a new collection using the items in this collection.
	 */
	public function collect(): Collection
	{
		return new Collection($this->items);
	}

	/**
	 * Combines the values of the current collection (as keys) with the values provided.
	 */
	public function combine(mixed $values): Collection
	{
		return new Collection(Arr::combine($this->items, $values));
	}

	/**
	 * Appends the values of a given array or collection onto the end of the current collection.
	 */
	public function concat(mixed $source): Collection
	{
		$result = new Collection($this);

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
		return Arr::contains($this->items, $value);
	}

	/**
	 * Checks whether all given values are present.
	 */
	public function containsAll(array $values): bool
	{
		return Arr::containsAll($this->items, $values);
	}

	/**
	 * Checks whether at least one of the given values is present.
	 */
	public function containsAny(array $values): bool
	{
		return Arr::containsAny($this->items, $values);
	}

	/**
	 * Checks whether none of the given values are present.
	 */
	public function containsNone(array $values): bool
	{
		return Arr::containsNone($this->items, $values);
	}

	/**
	 * Returns the total number of items in the collection, or the total number of items for the given key.
	 */
	public function count(string|null $key = null): int
	{
		return count($key !== null ? $this->only($key) : $this->items);
	}

	/**
	 * Counts all occurrences of each value in the collection. Optionally, uses a callback to group occurrences.
	 */
	public function countBy(callable|null $callback = null): Collection
	{
		if ($callback === null) {
			return new Collection(array_count_values($this->items));
		}

		$counts = [];
		foreach ($this->items as $key => $value) {
			$group = $callback($value, $key);
			if (isset($counts[$group]) === false) {
				$counts[$group] = 0;
			}
			$counts[$group]++;
		}

		return new Collection($counts);
	}

	/**
	 * @internal
	 */
	public function current(): mixed
	{
		return $this->items[$this->keys[$this->position]];
	}

	/**
	 * Returns the values in the current collection that are not present in the given collection.
	 */
	public function diff(mixed $items): Collection
	{
		return new Collection(Arr::diff($this->items, $items));
	}

	/**
	 * Returns the key and value pairs in the current collection that are not present in the given collection.
	 */
	public function diffAssoc(mixed $items): Collection
	{
		return new Collection(Arr::diffAssoc($this->items, $items));
	}

	/**
	 * Returns the keys with their values in the current collection that are not present in the given collection.
	 */
	public function diffKeys(mixed $items): Collection
	{
		return new Collection(Arr::diffKeys($this->items, $items));
	}

	/**
	 * Returns all duplicate values in the collection. Optionally, a key or callback can be provided.
	 */
	public function duplicates(callable|string|null $callback = null): Collection
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

		return new Collection($duplicates);
	}

	/**
	 * Iterates over all items in the collection and passes the item to the given callback. Stops iterating when `false` is returned, or the end of the collection is reached.
	 */
	public function each(callable $callback): Collection
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
	 * Creates a new collection using all items of the current collection except for those with the given key(s).
	 */
	public function except(string|array $keys): Collection
	{
		return new Collection(Arr::except($this->items, $keys));
	}

	/**
	 * Creates a new collection with each item only keeping the fields specified.
	 */
	public function fields(array $fields): Collection
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

		return new Collection($result);
	}

	/**
	 * Returns the items from the collection that pass a given truth test.
	 */
	public function filter(callable $callback): Collection
	{
		return new Collection(Arr::filter($this->items, $callback));
	}

	/**
	 * Returns the first item in the collection, optionally the first that passes a given truth test.
	 */
	public function first(callable|null $callback = null, mixed $default = null): mixed
	{
		return Arr::first($this->items, $callback, $default);
	}

	/**
	 * Swaps the keys with their corresponding values.
	 */
	public function flip(): Collection
	{
		return new Collection(array_flip($this->items));
	}

	/**
	 * Empties the collection completely.
	 */
	public function flush(): Collection
	{
		$this->items = [];
		$this->keys = array_values($this->keys);
		return $this;
	}

	/**
	 * Removes one or multiple items by their key(s).
	 */
	public function forget(mixed $key): Collection
	{
		$keys = is_array($key) ? $key : [$key];

		foreach ($keys as $key) {
			unset($this->items[$key]);
			unset($this->keys[array_search($key, $this->keys)]);
		}
		$this->keys = array_values($this->keys);

		return $this;
	}

	/**
	 * Returns the value for a given key. If the key does not exist, the default value is returned.
	 */
	public function get(mixed $key, mixed $default = null): mixed
	{
		return $this->offsetGet($key) ?? ($default instanceof Closure ? $default() : $default);
	}

	/**
	 * Return a new collection grouped by the given key, or a value returned from a callback.
	 */
	public function groupBy(callable|string $group_by, bool $preserve_keys = false): Collection
	{
		// Inspired by the Laravel Collection::groupBy() method.
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
					$results[$group_key] = new Collection();
				}

				$results[$group_key]->offsetSet($preserve_keys ? $key : null, $value);
			}
		}

		return new Collection($results);
	}

	/**
	 * Checks whether a key is present.
	 */
	public function has(mixed $key): bool
	{
		return Arr::has($this->items, $key);
	}

	/**
	 * Checks whether all given keys are present.
	 */
	public function hasAll(mixed $key): bool
	{
		return Arr::hasAll($this->items, $key);
	}

	/**
	 * Checks whether at least one of the given keys is present.
	 */
	public function hasAny(mixed $key): bool
	{
		return Arr::hasAny($this->items, $key);
	}

	/**
	 * Checks whether none of the given keys are present.
	 */
	public function hasNone(mixed $key): bool
	{
		return Arr::hasNone($this->items, $key);
	}

	/**
	 * Joins items in a collection together using "glue". When the collection contains arrays or objects, you need to provide the key you want to join.
	 */
	public function implode(string $value, string|null $glue = null): string
	{
		$first = $this->first();

		if (is_array($first) || (is_object($first) && !$first instanceof Stringable)) {
			return implode($glue ?? '', $this->pluck($value)->all());
		}

		return implode($value ?? '', $this->items);
	}

	/**
	 * Removes all values from the collection that are not present in the given collection.
	 */
	public function intersect(mixed $items): Collection
	{
		return new Collection(array_intersect($this->items, convert_to_array($items)));
	}

	/**
	 * Removes all keys from the collection that are not present in the given collection.
	 */
	public function intersectByKeys(mixed $items): Collection
	{
		return new Collection(array_intersect_key($this->items, convert_to_array($items)));
	}

	/**
	 * Returns `true` when no items are present in the collection.
	 */
	public function isEmpty(): bool
	{
		return empty($this->items);
	}

	/**
	 * Returns `true` when the collection keys are numeric, in ascending order, starting by 0.
	 */
	public function isList(): bool
	{
		return array_is_list($this->items);
	}

	/**
	 * Returns `true` when at least one item is present in the collection.
	 */
	public function isNotEmpty(): bool
	{
		return !empty($this->items);
	}

	/**
	 * Joins the values of the collection together. The second argument can be used to specify how the final element should be appended.
	 */
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
			return $this->first();
		}

		$collection = new Collection($this->items);
		$final_item = $collection->pop();

		return $collection->implode($glue).$final_glue.$final_item;
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
	public function key(): mixed
	{
		return $this->keys[$this->position];
	}

	/**
	 * Keys the collection using the given key, or the result of the callback.
	 */
	public function keyBy(callable|string $key_by): Collection
	{
		$key_by = value_retriever($key_by);
		$results = [];

		foreach ($this->items as $key => $item) {
			$results[(string) $key_by($item, $key)] = $item;
		}

		return new Collection($results);
	}

	/**
	 * Returns the keys present in the collection.
	 */
	public function keys(): Collection
	{
		return new Collection(array_keys($this->items));
	}

	/**
	 * Returns the last item in a collection, optionally the last the passes a given truth test.
	 */
	public function last(callable|null $callback = null, mixed $default = null): mixed
	{
		return Arr::last($this->items, $callback, $default);
	}

	/**
	 * Iterates through the collection allowing modification of each item, returning a new collection with the result.
	 */
	public function map(callable $callback): Collection
	{
		return new Collection(Arr::map($this->items, $callback));
	}

	/**
	 * Returns the highest value in a collection or for a given key, limited by the value of the second parameter.
	 */
	public function max(string|null $key = null, float|int|null $limit = null): float|int
	{
		return Arr::max($key !== null ? $this->pluck($key)->all() : $this->items, $limit);
	}

	/**
	 * Returns the median of the collection or for a given key.
	 */
	public function median(string|null $key = null): float|int|null
	{
		return Arr::median($key !== null ? $this->pluck($key)->all() : $this->items);
	}

	/**
	 *	Merges the current collection with the items in the new collection.
	 */
	public function merge(mixed $items): Collection
	{
		return new Collection(Arr::merge($this->items, $items));
	}

	/**
	 * Returns the lowest value in a collection or for a given key, limited by the value of the second parameter.
	 */
	public function min(string|null $key = null, float|int|null $limit = null): float|int
	{
		return Arr::min($key !== null ? $this->pluck($key)->all() : $this->items, $limit);
	}

	/**
	 * Checks whether the key or keys are missing.
	 */
	public function missing(string|int|array $key): bool
	{
		return Arr::missing($this->items, $key);
	}

	/**
	 *	Returns the mode of the collection or given key.
	 */
	public function mode(string|null $key = null): array
	{
		return Arr::mode($key !== null ? $this->pluck($key)->all() : $this->items);
	}

	/**
	 * Allows modifying the collection using a closure.
	 */
	public function modify(Closure $closure): Collection
	{
		$closure($this);
		return $this;
	}

	/**
	 * @internal
	 */
	public function next(): void
	{
		++$this->position;
	}

	/**
	 * Returns how many times a given value exists in the collection.
	 */
	public function occurrences(string $value): int
	{
		return $this->countBy()[$value] ?? 0;
	}

	/**
	 * Returns the item with the specified key, or all items with the specified key(s).
	 */
	public function only(string|array $keys): mixed
	{
		if (is_string($keys)) {
			return $this->get($keys);
		}
		return new Collection(Arr::only($this->items, $keys));
	}

	/**
	 * @internal
	 */
	public function valid(): bool
	{
		return isset($this->keys[$this->position]);
	}

	/**
	 * Splits the collection into two collections, where one has items passing the truth test, and the other has items failing the truth test.
	 */
	public function partition(callable $callback): Collection
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

		return new Collection([new Collection($passed), new Collection($failed)]);
	}

	/**
	 * Passes the collection to the callback and returns the result.
	 */
	public function pipe(callable $callback): mixed
	{
		return $callback($this);
	}

	/**
	 * Retrieves all values for a given key.
	 */
	public function pluck(string $value, string|null $key = null): Collection
	{
		return new Collection(Arr::pluck($this->items, $value, $key));
	}

	/**
	 * Removes and returns the last item or `x` items from the collection. Defaults to `1`.
	 */
	public function pop(int $count = 1): mixed
	{
		if ($count === 1) {
			$value = array_pop($this->items);
			$this->keys = array_keys($this->items);
			return $value;
		}

		if ($this->isEmpty()) {
			return null;
		}

		$results = [];
		$item_count = $this->count();

		foreach (range(1, min($count, $item_count)) as $ignored) {
			$results[] = array_pop($this->items);
		}

		$this->keys = array_keys($this->items);
		return new Collection($results);
	}

	/**
	 * Adds the value or values to the beginning of the collection. Does not return a new collection.
	 */
	public function prepend(mixed $value, string|int|null $key = null): Collection
	{
		$collection = new Collection();
		$this->items = $collection->put($key, $value)->concat($this->items)->all();
		$this->keys = array_keys($this->items);
		return $this;
	}

	/**
	 * Retrieves the item for the given key and removes it from the collection.
	 */
	public function pull(mixed $key, mixed $default = null): mixed
	{
		$value = $this->offsetGet($key) ?? $default;
		$this->offsetUnset($key);
		return $value;
	}

	/**
	 * Adds a new value to the end of the collection.
	 */
	public function push(mixed $value): Collection
	{
		$this->offsetSet(null, $value);
		return $this;
	}

	/**
	 * Adds a new value for the given key.
	 */
	public function put(mixed $key, mixed $value): Collection
	{
		$this->offsetSet($key, $value);
		return $this;
	}

	/**
	 * Returns `x` random items from the collection. Defaults to `1`.
	 */
	public function random(int $items = 1): mixed
	{
		return Arr::random($this->items, $items);
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
	 * Reduces the collection to a single value, passing the result of each iteration into the next.
	 */
	public function reduce(callable $callback, mixed $initial = null): mixed
	{
		return Arr::reduce($this->items, $callback, $initial);
	}

	/**
	 * Returns all items from the collection except those that pass the truth test.
	 */
	public function reject(callable $callback): Collection
	{
		$new = [];
		foreach ($this->items as $key => $value) {
			if ($callback($value, $key) === false) {
				$new[$key] = $value;
			}
		}
		return new Collection($new);
	}

	/**
	 * Replaces the items in the current collection with the items in the given collection.
	 */
	public function replace(mixed $items): Collection
	{
		return new Collection(Arr::replace($this->items, $items));
	}

	/**
	 * Similar to `replace()`, but applies the same process to inner values.
	 */
	public function replaceRecursive(mixed $items): Collection
	{
		return new Collection(Arr::replaceRecursive($this->items, $items));
	}

	/**
	 * Remove existing keys and replace them with consecutive keys starting from 0.
	 */
	public function resetKeys(): Collection
	{
		return new Collection(array_values($this->items));
	}

	/**
	 * Reverses the order of the items in the collection while preserving the keys.
	 */
	public function reverse(): Collection
	{
		return new Collection(array_reverse($this->items, true));
	}

	/**
	 * @internal
	 */
	public function rewind(): void
	{
		$this->position = 0;
	}

	/**
	 * Returns the corresponding key of the searched value when found. Uses strict comparisons by default.
	 * Optionally, you can pass a closure to search for the first item that matches a truth test.
	 */
	public function search(mixed $value, bool $strict = true): string|int|bool
	{
		return Arr::search($this->items, $value, $strict);
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
		return new Collection($results);
	}

	/**
	 * Changes the order of the items in the collection to be random.
	 */
	public function shuffle(int|null $seed = null): Collection
	{
		return new Collection(Arr::shuffle($this->items, $seed));
	}

	/**
	 * Skips over `x` items from the collection, and returns the remaining items.
	 */
	public function skip(int $count): Collection
	{
		if ($this->count() <= $count) {
			return new Collection();
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
		return new Collection($items);
	}

	/**
	 * Skips over the items from the collection until the closure returns `true`, and returns the remaining items.
	 */
	public function skipUntil(mixed $target): Collection
	{
		if ($this->count() === 0) {
			return new Collection();
		}

		$items = $this->items;
		foreach ($this->items as $key => $value) {
			if (($target instanceof Closure && $target($value, $key)) || $target === $value) {
				break;
			}
			unset($items[$key]);
		}
		return new Collection($items);
	}

	/**
	 * Skips over the items from the collection as long as the closure returns `true`, and returns the remaining items.
	 */
	public function skipWhile(Closure $closure): Collection
	{
		if ($this->count() === 0) {
			return new Collection();
		}

		$items = $this->items;
		foreach ($this->items as $key => $value) {
			if ($closure($value, $key)) {
				unset($items[$key]);
				continue;
			}
			break;
		}
		return new Collection($items);
	}

	/**
	 * Returns a slice of the collection starting at the given index, with a maximum number of items if defined.
	 */
	public function slice(int $offset, int|null $length = null, bool $preserve_keys = true): Collection
	{
		return new Collection(array_slice($this->items, $offset, $length, $preserve_keys));
	}

	/**
	 * Sorts the array by value, while preserving the array keys. For custom behavior, pass your own algorithm to the `callback` parameter.
	 */
	public function sort(callable|int|null $callback = null, bool $descending = false): Collection
	{
		return new Collection(Arr::sort($this->items, $callback, $descending));
	}

	/**
	 * Sorts the array by value in descending order, while preserving the array keys.
	 */
	public function sortDesc(int $options = SORT_REGULAR): Collection
	{
		return new Collection(Arr::sortDesc($this->items, $options));
	}

	/**
	 * Sorts the array by key.
	 */
	public function sortKeys(int $options = null, bool $descending = false): Collection
	{
		return new Collection(Arr::sortKeys($this->items, $options, $descending));
	}

	/**
	 * Sorts the array by key in descending order.
	 */
	public function sortKeysDesc(int $options = SORT_REGULAR): Collection
	{
		return new Collection(Arr::sortKeysDesc($this->items, $options));
	}

	/**
	 * Sort the items using the result of the provided closure.
	 */
	public function sortBy(mixed $closure, int $options = SORT_REGULAR, bool $descending = false): Collection
	{
		return new Collection(Arr::sortBy($this->items, $closure, $options, $descending));
	}

	/**
	 * Sort the items in descending order using the result of the provided closure.
	 */
	public function sortByDesc(mixed $closure, int $options = SORT_REGULAR): Collection
	{
		return new Collection(Arr::sortByDesc($this->items, $closure, $options));
	}

	/**
	 * Returns the sum of all items in the collection, the specified key or using a closure.
	 */
	public function sum(Closure|string|null $closure = null): int|float
	{
		return Arr::sum($this->items, $closure);
	}

	/**
	 * Returns `x` number of items from the original collection.
	 */
	public function take(int $count): Collection
	{
		if ($count < 0) {
			return $this->slice($count, abs($count));
		}

		return $this->slice(0, $count);
	}

	/**
	 * Returns all items after the specified value or when an item passes the truth test.
	 */
	public function takeFrom(mixed $closure): Collection
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
		return new Collection($result);
	}

	/**
	 * Returns all items up until the value has been found or an item passes a truth test.
	 */
	public function takeUntil(mixed $closure): Collection
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
		return new Collection($result);
	}

	/**
	 * Returns all items until an item fails the given truth test.
	 */
	public function takeWhile(Closure $closure): Collection
	{
		$result = [];
		foreach ($this->items as $key => $value) {
			if ($closure($value, $key) === false) {
				break;
			} else {
				$result[$key] = $value;
			}
		}
		return new Collection($result);
	}

	/**
	 * Iterates over all items in the collection and replaces all values with the values returned by the callback.
	 */
	public function transform(callable $callback): Collection
	{
		$this->items = Arr::map($this->items, $callback);
		return $this;
	}

	/**
	 * Returns the values of the collection without the original keys.
	 */
	public function values(): Collection
	{
		return new Collection(array_values($this->items));
	}

	/**
	 * Executes the callback when no items are present. Optionally, when not empty, the alternative callback will be executed.
	 */
	public function whenEmpty(callable $callback, callable|null $alternative = null): Collection
	{
		return $this->when($this->isEmpty(), $callback, $alternative);
	}

	/**
	 * Executes the callback when at least one item is present. Optionally, when empty, the alternative callback will be executed.
	 */
	public function whenNotEmpty(callable $callback, callable|null $alternative = null): Collection
	{
		return $this->when($this->isNotEmpty(), $callback, $alternative);
	}

	/**
	 * @internal
	 */
	public function offsetExists(mixed $offset): bool
	{
		return isset($this->items[$offset]);
	}

	/**
	 * @internal
	 */
	public function offsetGet(mixed $offset): mixed
	{
		return $this->items[$offset] ?? null;
	}

	/**
	 * @internal
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		if (is_null($offset)) {
			$this->items[] = $value;
			$this->keys[] = array_key_last($this->items);
		} else {
			$this->items[$offset] = $value;
			if (!in_array($offset, $this->keys)) $this->keys[] = $offset;
		}
	}

	/**
	 * @internal
	 */
	public function offsetUnset(mixed $offset): void
	{
		unset($this->items[$offset]);
		unset($this->keys[array_search($offset, $this->keys)]);
		$this->keys = array_values($this->keys);
	}

	/**
	 * Returns a formatted query string using the items in the collection.
	 */
	public function toQuery(bool $encode = true): string
	{
		return Arr::toQueryString($this->items, $encode);
	}

	/**
	 * Returns JSON representation created from the data in the collection.
	 */
	public function toJson(): string
	{
		return json_encode_clean($this->jsonSerialize());
	}

	/**
	 * Returns array representation created from the data in the collection.
	 */
	public function toArray(): array
	{
		return $this->items;
	}

	/**
	 * Returns all items where the value is the equivalent to `null`.
	 */
	public function whereNull(): Collection
	{
		return new Collection(Arr::whereNull($this->items));
	}

	/**
	 * Returns all items where the value is not equivalent to `null`.
	 */
	public function whereNotNull(): Collection
	{
		return new Collection(Arr::whereNotNull($this->items));
	}

}