<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Casts;

use Rovota\Core\Structures\Map;

final class MapCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return $value instanceof Map;
	}

	public function supportsValue(mixed $value): bool
	{
		return $value instanceof Map;
	}

	// -----------------

	public function get(mixed $value, array $options): Map
	{
		$separator = $options[0] ?? ',';
		if(str_contains($value, $separator)) {
			$items = explode($separator, $value);
		} else {
			$items = strlen($value) > 0 ? [$value] : [];
		}
		return new Map($items);
	}

	public function set(mixed $value, array $options): string
	{
		return implode($options[0] ?? ',', $value->toArray());
	}

}