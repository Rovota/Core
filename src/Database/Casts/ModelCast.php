<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Database\Casts;

use Rovota\Core\Database\Model;

final class ModelCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return $value instanceof Model;
	}

	public function supportsValue(mixed $value): bool
	{
		return $value instanceof Model;
	}

	// -----------------

	public function get(mixed $value, array $options): Model|null
	{
		return $options[0]::find($value, $options[1] ?? 'id');
	}

	public function set(mixed $value, array $options): string
	{
		return $value->getAttribute($options[1] ?? 'id');
	}

}