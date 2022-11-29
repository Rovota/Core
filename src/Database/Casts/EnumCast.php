<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Casts;

use BackedEnum;
use Rovota\Core\Support\Arr;

final class EnumCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		if (Arr::contains($options[0]::cases(), $value) === false) {
			return false;
		}
		return $value instanceof BackedEnum;
	}

	public function supportsValue(mixed $value): bool
	{
		return $value instanceof BackedEnum;
	}

	// -----------------

	public function get(mixed $value, array $options): BackedEnum
	{
		return $options[0]::from($value);
	}

	public function set(mixed $value, array $options): string|int
	{
		return $value->value;
	}

}