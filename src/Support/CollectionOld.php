<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 *
 * Inspired by from the Laravel CollectionOld class.
 */

namespace Rovota\Core\Support;

final class CollectionOld
{

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

}