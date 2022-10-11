<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Database\Casts;

final class StringCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return is_string($value);
	}

	// -----------------

	public function get(mixed $value, array $options): string
	{
		return (string)$value;
	}

	public function set(mixed $value, array $options): string
	{
		return (string)$value;
	}

}