<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Database\Casts;

final class SerialCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return true;
	}

	// -----------------

	public function get(mixed $value, array $options): mixed
	{
		return unserialize($value);
	}

	public function set(mixed $value, array $options): string
	{
		return serialize($value);
	}

}