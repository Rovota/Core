<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Casts;

use Rovota\Core\Support\Text;

final class TextCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return $value instanceof Text;
	}

	public function supportsValue(mixed $value): bool
	{
		return $value instanceof Text;
	}

	// -----------------

	public function get(mixed $value, array $options): Text
	{
		return string($value);
	}

	public function set(mixed $value, array $options): string
	{
		return (string)$value;
	}

}