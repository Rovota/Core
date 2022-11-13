<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Casts;

use Rovota\Core\Support\Collection;

final class CollectionCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return $value instanceof Collection;
	}

	public function supportsValue(mixed $value): bool
	{
		return $value instanceof Collection;
	}

	// -----------------

	public function get(mixed $value, array $options): Collection
	{
		$separator = $options[0] ?? ',';
		if(str_contains($value, $separator)) {
			$items = explode($separator, $value);
		} else {
			$items = strlen($value) > 0 ? [$value] : [];
		}
		return new Collection($items);
	}

	public function set(mixed $value, array $options): string
	{
		return implode($options[0] ?? ',', $value->all());
	}

}