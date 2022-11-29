<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Casts;

use Rovota\Core\Support\FluentString;

final class FluentStringCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return $value instanceof FluentString;
	}

	public function supportsValue(mixed $value): bool
	{
		return $value instanceof FluentString;
	}

	// -----------------

	public function get(mixed $value, array $options): FluentString
	{
		return string($value);
	}

	public function set(mixed $value, array $options): string
	{
		return (string)$value;
	}

}