<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support;

use ArrayAccess;
use Closure;
use Rovota\Core\Support\Helpers\Arr;

final class ArrOld
{

	// -----------------

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

		return Arr::average([$values[$middle - 1], $values[$middle]]);
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

	public static function replace(array $array, mixed $items): array
	{
		return array_replace($array, convert_to_array($items));
	}

	public static function replaceRecursive(array $array, mixed $items): array
	{
		return array_replace_recursive($array, convert_to_array($items));
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

	public static function whereNull(array $array): array
	{
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = ArrOld::whereNull($value);
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
				$array[$key] = ArrOld::whereNotNull($value);
			}

			if ($array[$key] === null) {
				unset($array[$key]);
			}
		}

		return $array;
	}

}