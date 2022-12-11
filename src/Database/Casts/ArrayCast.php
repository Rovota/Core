<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Casts;

final class ArrayCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return is_array($value);
	}

	public function supportsValue(mixed $value): bool
	{
		return is_array($value);
	}

	// -----------------

	public function get(mixed $value, array $options): array
	{
		$separator = $options[0] ?? ',';
		if(str_contains($value, $separator)) {
			$items = explode($separator, $value);
		} else {
			$items = mb_strlen($value) > 0 ? [$value] : [];
		}
		return $items;
	}

	public function set(mixed $value, array $options): string
	{
		return implode($options[0] ?? ',', $value);
	}

}