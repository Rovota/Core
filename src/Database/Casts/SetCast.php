<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Casts;

use Rovota\Core\Structures\Set;

final class SetCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return $value instanceof Set;
	}

	public function supportsValue(mixed $value): bool
	{
		return $value instanceof Set;
	}

	// -----------------

	public function get(mixed $value, array $options): Set
	{
		$separator = $options[0] ?? ',';
		if(str_contains($value, $separator)) {
			$items = explode($separator, $value);
		} else {
			$items = mb_strlen($value) > 0 ? [$value] : [];
		}
		return new Set($items);
	}

	public function set(mixed $value, array $options): string
	{
		return implode($options[0] ?? ',', $value->toArray());
	}

}