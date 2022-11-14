<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support\Helpers;

use ArrayAccess;

final class Arr
{

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * Determines whether the given value is an accessible array or implements ArrayAccess.
	 */
	public static function accessible(mixed $value): bool
	{
		return is_array($value) || $value instanceof ArrayAccess;
	}

	/**
	 * Returns the average of a given array. When the array is empty or contains non-numeric values, `0` will be returned.
	 */
	public static function average(mixed $array, bool $round = false, int $precision = 0): float|int
	{
		$array = convert_to_array($array);
		$count = count($array);

		if ($count < 2) {
			return array_pop($array) ?? 0;
		}

		$average = array_sum($array) / $count;
		return $round ? round($average, $precision) : $average;
	}

	/**
	 * Returns how many elements are in the given array.
	 */
	public static function count(mixed $array): int
	{
		return count(convert_to_array($array));
	}

	/**
	 * Returns the items from the array that pass a given truth test.
	 */
	public static function filter(mixed $array, callable $callback): array
	{
		$new = [];
		foreach (convert_to_array($array) as $key => $value) {
			if ($callback($value, $key) === true) {
				$new[$key] = $value;
			}
		}

		return $new;
	}

	/**
	 * Returns the first item in the array, optionally the first that passes a given truth test.
	 */
	public static function first(mixed $array, callable|null $callback = null, mixed $default = null): mixed
	{
		$array = convert_to_array($array);

		if (is_null($callback)) {
			if (empty($array)) {
				return $default;
			}
			foreach ($array as $item) {
				return $item;
			}
		}

		foreach ($array as $key => $value) {
			if ($callback($value, $key)) {
				return $value;
			}
		}

		return $default;
	}

	public static function last(mixed $array, callable|null $callback = null, mixed $default = null): mixed
	{
		$array = convert_to_array($array);

		if (is_null($callback)) {
			return empty($array) ? $default : end($array);
		}

		return Arr::first(array_reverse($array, true), $callback, $default);
	}

	/**
	 * Returns the highest value present in the array.
	 */
	public static function max(mixed $array, float|int|null $limit = null): float|int
	{
		$maximum = max(convert_to_array($array));
		return ($limit !== null && $maximum >= $limit) ? $limit : $maximum;
	}

	/**
	 * Merges the given arrays into a single array. Existing keys will be overwritten.
	 */
	public static function merge(mixed $first, mixed $second): array
	{
		return array_merge(convert_to_array($first), convert_to_array($second));
	}

	/**
	 * Returns the lowest value present in the array.
	 */
	public static function min(mixed $array, float|int|null $limit = null): float|int
	{
		$minimum = min(convert_to_array($array));
		return ($limit !== null && $minimum <= $limit) ? $limit : $minimum;
	}

	/**
	 * Returns a new array with each entry only containing the specified field, optionally keyed by the given key.
	 */
	public static function pluck(mixed $array, string $field, string|null $key = null): array
	{
		$results = [];
		$array = convert_to_array($array);
		$fields = explode('.', $field);
		$key = is_string($key) ? explode('.', $key) : $key;

		foreach ($array as $item) {
			$item_value = data_get($item, $fields);
			if (is_null($key)) {
				$results[] = $item_value;
			} else {
				$item_key = data_get($item, $key);
				if (is_object($item_key) && method_exists($item_key, '__toString')) {
					$item_key = (string)$item_key;
				}
				$results[$item_key] = $item_value;
			}
		}

		return $results;
	}

	/**
	 * Reduces the array to a single value, passing the result of each iteration into the next:
	 */
	public static function reduce(mixed $array, callable $callback, mixed $initial = null): mixed
	{
		$result = $initial;
		foreach (convert_to_array($array) as $key => $value) {
			$result = $callback($result, $value, $key);
		}
		return $result;
	}

	/**
	 * Returns the sum of all items in the array, the specified key or using a closure:
	 */
	public static function sum(mixed $array, callable|string|null $callback = null): int|float
	{
		$callback = is_null($callback) ? self::valueCallable() : value_retriever($callback);
		return self::reduce(convert_to_array($array), function ($result, $item) use ($callback) {
			return $result + $callback($item);
		}, 0);
	}

	// -----------------

	protected static function valueCallable(): callable
	{
		return function ($value) {
			return $value;
		};
	}

}