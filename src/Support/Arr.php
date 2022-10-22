<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Support;

use ArrayAccess;
use Closure;

final class Arr
{

	protected function __construct()
	{
	}

	// -----------------

	public static function make(string $string): Collection
	{
		return new Collection($string);
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
	 * Retrieve the average value of the array.
	 */
	public static function avg(array $array, bool $round = false): float|int|null
	{
		$count = count($array);
		if ($count === 0) return null;
		$average = array_sum($array) / $count;
		return $round ? round($average) : $average;
	}

	/**
	 * Collapse an array or collection of arrays into a single, flat array.
	 */
	public static function collapse(array|Collection $array): array
	{
		$normalized = [];

		foreach ($array as $item) {
			if ($item instanceof Collection) {
				$item = $item->all();
			} else {
				if (!is_array($item)) {
					continue;
				}
			}

			$normalized[] = $item;
		}

		return array_merge([], ...$normalized);
	}

	public static function combine(mixed $keys, mixed $values): array
	{
		return array_combine(convert_to_array($keys), convert_to_array($values));
	}

	public static function contains(array $haystack, mixed $value): bool
	{
		if ($value instanceof Closure) {
			$callback = $value;
			foreach ($haystack as $key => $value) {
				if ($callback($value, $key)) {
					return $value;
				}
			}
			return false;
		}
		return in_array($value, $haystack, true);
	}

	public static function containsAll(array $haystack, array $values): bool
	{
		foreach ($values as $value) {
			if (!in_array($value, $haystack, true)) {
				return false;
			}
		}
		return true;
	}

	public static function containsAny(array $haystack, array $values): bool
	{
		foreach ($values as $value) {
			if (in_array($value, $haystack, true)) {
				return true;
			}
		}
		return false;
	}

	public static function containsNone(array $haystack, array $values): bool
	{
		foreach ($values as $value) {
			if (in_array($value, $haystack, true)) {
				return false;
			}
		}
		return true;
	}

	public static function diff(mixed $first, mixed $second): array
	{
		return array_diff(convert_to_array($first), convert_to_array($second));
	}

	public static function diffAssoc(mixed $first, mixed $second): array
	{
		return array_diff_assoc(convert_to_array($first), convert_to_array($second));
	}

	public static function diffKeys(mixed $first, mixed $second): array
	{
		return array_diff_key(convert_to_array($first), convert_to_array($second));
	}

	public static function exists(mixed $array, string|int $key): bool
	{
		if ($array instanceof ArrayAccess) {
			return $array->offsetExists($key);
		}

		return array_key_exists($key, $array);
	}

	public static function except(array $array, string|array $keys): array
	{
		if (is_string($keys)) {
			$keys = [$keys];
		}

		foreach ($keys as $key) {
			unset($array[$key]);
		}

		return $array;
	}

	public static function filter(array $array, callable|null $callback = null): array
	{
		$new = [];
		foreach ($array as $key => $value) {
			if ($callback === null) {
				if ($value !== null) {
					$new[$key] = $value;
				}
				continue;
			}
			if ($callback($value, $key) === true) {
				$new[$key] = $value;
			}
		}

		return $new;
	}

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
			if ($callback($key, $value)) {
				return $value;
			}
		}

		return $default;
	}

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

	public static function has(array $array, mixed $key): bool
	{
		$keys = is_array($key) ? $key : [$key];
		foreach ($keys as $key) {
			if (!array_key_exists($key, $array)) {
				return false;
			}
		}
		return true;
	}

	public static function hasAll(array $array, mixed $key): bool
	{
		$keys = is_array($key) ? $key : [$key];
		foreach ($keys as $key) {
			if (!array_key_exists($key, $array)) {
				return false;
			}
		}
		return true;
	}

	public static function hasAny(array $array, mixed $key): bool
	{
		$keys = is_array($key) ? $key : [$key];
		foreach ($keys as $key) {
			if (array_key_exists($key, $array)) {
				return true;
			}
		}
		return false;
	}

	public static function hasNone(array $array, mixed $key): bool
	{
		$keys = is_array($key) ? $key : [$key];
		foreach ($keys as $key) {
			if (array_key_exists($key, $array)) {
				return false;
			}
		}
		return true;
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

	public static function max(array $array, float|int|null $limit = null): float|int
	{
		$value = max($array);
		return ($limit !== null && $value >= $limit) ? $limit : $value;
	}

	public static function median(array $array): float|int|null
	{
		$count = count($array);
		sort($array);

		$values = array_values($array);

		if ($count === 0) {
			return null;
		}

		$middle = (int) ($count / 2);

		if ($count % 2) {
			return $values[$middle];
		}

		return Arr::avg([$values[$middle - 1], $values[$middle]]);
	}

	public static function merge(mixed $first, mixed $second): array
	{
		return array_merge(convert_to_array($first), convert_to_array($second));
	}

	public static function min(array $array, float|int|null $limit = null): float|int
	{
		$value = min($array);
		return ($limit !== null && $value <= $limit) ? $limit : $value;
	}

	public static function missing(array $array, string|int|array $key): bool
	{
		$keys = is_array($key) ? $key : [$key];
		foreach ($keys as $key) {
			if (!array_key_exists($key, $array)) {
				return true;
			}
		}
		return false;
	}

	public static function mode(array $array): array|null
	{
		if (count($array) === 0) {
			return null;
		}

		$appearances = [];

		foreach ($array as $item) {
			if (!isset($appearances[$item])) {
				$appearances[$item] = 0;
			}
			$appearances[$item]++;
		}

		$modes = array_keys($appearances, max($appearances));
		sort($modes);
		return $modes;
	}

	public static function only(array $array, array $keys): array
	{
		$new = [];
		foreach ($array as $key => $value) {
			if (in_array($key, $keys)) {
				$new[$key] = $value;
			}
		}
		return $new;
	}

	public static function pluck(array $array, string $value, string|null $key = null): array
	{
		$results = [];
		$value = explode('.', $value);
		$key = is_string($key) ? explode('.', $key) : $key;

		foreach ($array as $item) {
			$item_value = data_get($item, $value);

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

	public static function random(array $array, int $items = 1): mixed
	{
		$count = count($array);
		$requested = $items === 0 ? 1 : (($items > $count) ? $count : $items);

		if ($requested === 1) {
			return $array[array_rand($array)];
		}

		$keys = array_rand($array, $requested);
		$result = [];

		foreach ($keys as $key) {
			$result[$key] = $array[$key];
		}

		return $result;
	}

	public static function reduce(array $array, callable $callback, mixed $initial = null): mixed
	{
		$result = $initial;

		foreach ($array as $key => $value) {
			$result = $callback($result, $value, $key);
		}

		return $result;
	}

	public static function replace(array $array, mixed $items): array
	{
		return array_replace($array, convert_to_array($items));
	}

	public static function replaceRecursive(array $array, mixed $items): array
	{
		return array_replace_recursive($array, convert_to_array($items));
	}

	public static function search(array $array, mixed $value, bool $strict = true): string|int|bool
	{
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

	public static function shuffle(array $array, int|null $seed = null): array
	{
		if (is_null($seed)) {
			shuffle($array);
		} else {
			mt_srand($seed);
			shuffle($array);
			mt_srand();
		}

		return $array;
	}

	public static function sort(array $array, callable|int|null $callback = null, bool $descending = false): array
	{
		if (is_callable($callback)) {
			uasort($array, $callback);
		} else {
			$descending ? arsort($array, $callback ?? SORT_REGULAR) : asort($array, $callback ?? SORT_REGULAR);
		}
		return $array;
	}

	public static function sortDesc(array $array, int $options = SORT_REGULAR): array
	{
		return self::sort($array, $options, true);
	}

	public static function sortKeys(array $array, int $options = null, bool $descending = false): array
	{
		$descending ? krsort($array, $options) : ksort($array, $options);
		return $array;
	}

	public static function sortKeysDesc(array $array, int $options = SORT_REGULAR): array
	{
		return self::sortKeys($array, $options, true);
	}

	public static function sortBy(array $array, mixed $callback, int $options = SORT_REGULAR, bool $descending = false): array
	{
		$results = [];
		$callback = value_retriever($callback);

		foreach ($array as $key => $value) {
			$results[$key] = $callback($value, $key);
		}

		$descending ? arsort($results, $options) : asort($results, $options);

		foreach (array_keys($results) as $key) {
			$results[$key] = $array[$key];
		}

		return $results;
	}

	public static function sortByDesc(array $array, mixed $callback, int $options = SORT_REGULAR): array
	{
		return self::sortBy($array, $callback, $options, true);
	}

	public static function sum(array $array, callable|string|null $callback = null): int|float
	{
		$callback = is_null($callback) ? self::valueCallable() : value_retriever($callback);
		return self::reduce($array, function ($result, $item) use ($callback) {
			return $result + $callback($item);
		}, 0);
	}

	public static function whereNull(array $array): array
	{
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = Arr::whereNull($value);
			}

			if ($array[$key] !== null) {
				unset($array[$key]);
			}
		}

		return $array;
	}

	public static function whereNotNull(array $array): array
	{
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = Arr::whereNotNull($value);
			}

			if ($array[$key] === null) {
				unset($array[$key]);
			}
		}

		return $array;
	}

	// -----------------

	public static function toQueryString(array $fields = [], bool $encode = true): string
	{
		$items = '';
		foreach ($fields as $key => $value) {
			$value = (string)$value;
			if (Text::length($value) > 0) {
				$value = $encode ? rawurlencode($value) : $value;
				$items .= sprintf('%s%s=%s', (Text::length($items) > 0) ? '&' : '', $key, $value);
			}
		}
		return (Text::length($items) > 0) ? '?'.$items : '';
	}

	// -----------------

	protected static function valueCallable(): callable
	{
		return function ($value) {
			return $value;
		};
	}

}