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
	 * Passes the collection to the callback and returns the result.
	 */
	public function pipe(callable $callback): mixed
	{
		return $callback($this);
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

}