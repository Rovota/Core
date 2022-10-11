<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Database\Casts;

use Stringable;

final class ObjectCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return $value instanceof Stringable;
	}

	public function supportsValue(mixed $value): bool
	{
		return $value instanceof Stringable;
	}

	// -----------------

	public function get(mixed $value, array $options): Object|null
	{
		return new $options[0]($value);
	}

	public function set(mixed $value, array $options): string
	{
		return (string) $value;
	}

}