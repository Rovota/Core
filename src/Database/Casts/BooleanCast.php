<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Database\Casts;

final class BooleanCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return is_bool($value);
	}

	public function supportsValue(mixed $value): bool
	{
		return is_bool($value);
	}

	// -----------------

	public function get(mixed $value, array $options): bool
	{
		return (int)$value === 1;
	}

	public function set(mixed $value, array $options): int
	{
		return $value ? 1 : 0;
	}

}