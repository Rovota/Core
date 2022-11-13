<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Casts;

final class FloatCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return is_float($value);
	}

	public function supportsValue(mixed $value): bool
	{
		return is_float($value);
	}

	// -----------------

	public function get(mixed $value, array $options): float
	{
		return (float)$value;
	}

	public function set(mixed $value, array $options): string
	{
		return (string)$value;
	}

}