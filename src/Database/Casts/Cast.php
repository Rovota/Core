<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Casts;

abstract class Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return false;
	}

	public function supportsValue(mixed $value): bool
	{
		return false;
	}

	// -----------------

	public function get(mixed $value, array $options): mixed
	{
		return $value;
	}

	public function set(mixed $value, array $options): mixed
	{
		return $value;
	}

}