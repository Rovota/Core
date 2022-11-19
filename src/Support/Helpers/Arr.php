<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support\Helpers;

use ArrayAccess;
use Closure;
use Rovota\Core\Support\Text;

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
	public static function average(array $array, bool $round = false, int $precision = 0): float|int
	{
		$count = count($array);

		if ($count < 2) {
			return array_pop($array) ?? 0;
		}

		$average = array_sum($array) / $count;
		return $round ? round($average, $precision) : $average;
	}

	/**
	 * Returns the items from the array that pass a given truth test.
	 */
	public static function filter(array $array, callable $callback): array
	{
		$new = [];
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$new[$key] = Arr::filter($value, $callback);
				if (empty($new[$key])) {
					unset($new[$key]);
				}
			} else {
				if ($callback($value, $key) === true) {
					$new[$key] = $value;
				}
			}
		}

		return $new;
	}

	/**
	 * Returns the first item in the array, optionally the first that passes a given truth test.
	 */
	public static function first(array $array, callable|null $callback = null, mixed $default = null): mixed
	{
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

	public static function last(array $array, callable|null $callback = null, mixed $default = null): mixed
	{
		if (is_null($callback)) {
			return empty($array) ? $default : end($array);
		}

		return Arr::first(array_reverse($array, true), $callback, $default);
	}

	public static function map(array $array, callable $callback): array
	{
		$keys = array_keys($array);
		$items = array_map($callback, $array, $keys);

		return array_combine($keys, $items);
	}

	/**
	 * Returns the highest value present in the array.
	 */
	public static function max(array $array, float|int|null $limit = null): float|int
	{
		$maximum = max($array);
		return ($limit !== null && $maximum >= $limit) ? $limit : $maximum;
	}

	/**
	 * Merges the given arrays into a single array. Existing keys will be overwritten.
	 */
	public static function merge(mixed $first, mixed $second): array
	{
		return array_merge($first, $second);
	}

	/**
	 * Returns the lowest value present in the array.
	 */
	public static function min(array $array, float|int|null $limit = null): float|int
	{
		$minimum = min($array);
		return ($limit !== null && $minimum <= $limit) ? $limit : $minimum;
	}

	/**
	 * Returns a new array with each entry only containing the specified field, optionally keyed by the given key.
	 */
	public static function pluck(array $array, string $field, string|null $key = null): array
	{
		$results = [];
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
	public static function reduce(array $array, callable $callback, mixed $initial = null): mixed
	{
		$result = $initial;
		foreach ($array as $key => $value) {
			$result = $callback($result, $value, $key);
		}
		return $result;
	}

	/**
	 * Returns the corresponding key of the searched value when found. Uses strict comparisons by default.
	 * Optionally, you can pass a closure to search for the first item that matches a truth test.
	 */
	public static function search(array $array, mixed $value, bool $strict = true): string|int|bool
	{
		if (is_object($value)) {
			$value = spl_object_hash($value);
		}

		if ($value instanceof Closure === false) {
			return array_search($value, $array, $strict);
		}

		$callable = $value;
		foreach ($array as $key => $value) {
			if ($callable($value, $key)) {
				return $key;
			}
		}

		return false;
	}

	public static function sort(array $array, callable|null $callback = null, bool $descending = false): array
	{
		if (is_callable($callback)) {
			uasort($array, $callback);
		} else {
			$descending ? arsort($array) : asort($array);
		}
		return $array;
	}

	public static function sortBy(array $array, mixed $callback, bool $descending = false): array
	{
		$results = [];
		$callback = value_retriever($callback);

		foreach ($array as $key => $value) {
			$results[$key] = $callback($value, $key);
		}

		$descending ? arsort($results) : asort($results);

		foreach (array_keys($results) as $key) {
			$results[$key] = $array[$key];
		}

		return $results;
	}

	public static function sortKeys(array $array, bool $descending = false): array
	{
		$descending ? krsort($array) : ksort($array);
		return $array;
	}

	/**
	 * Returns the sum of all items in the array, the specified key or using a closure:
	 */
	public static function sum(array $array, callable|string|null $callback = null): int|float
	{
		$callback = is_null($callback) ? self::valueCallable() : value_retriever($callback);
		return self::reduce($array, function ($result, $item) use ($callback) {
			return $result + $callback($item);
		}, 0);
	}

	// -----------------

	public static function fromAcceptHeader(string|null $header): array
	{
		$header = trim($header ?? '');
		if (strlen($header) === 0) {
			return [];
		}
		return array_reduce(explode(',', $header),
			function ($carry, $element) {
				$type = Text::before($element, ';');
				$quality = str_contains($element, ';q=') ? Text::afterLast($element, ';q=') : 1.00;
				$carry[trim($type)] = (float) $quality;
				return $carry;
			},[]
		);
	}

	// -----------------

	protected static function valueCallable(): callable
	{
		return function ($value) {
			return $value;
		};
	}

}