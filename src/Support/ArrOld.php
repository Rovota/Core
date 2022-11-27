<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support;

final class ArrOld
{

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

	public static function replace(array $array, mixed $items): array
	{
		return array_replace($array, convert_to_array($items));
	}

	public static function replaceRecursive(array $array, mixed $items): array
	{
		return array_replace_recursive($array, convert_to_array($items));
	}

}