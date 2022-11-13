<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Casts;

final class IntegerCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return is_int($value);
	}

	// -----------------

	public function get(mixed $value, array $options): int
	{
		return (int)$value;
	}

	public function set(mixed $value, array $options): int
	{
		return (int)$value;
	}

}