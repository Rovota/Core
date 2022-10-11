<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Database\Casts;

use Rovota\Core\Support\Json;

final class JsonCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return true;
	}

	// -----------------

	public function get(mixed $value, array $options): array
	{
		return Json::decode($value, isset($options[0]) && $options[0] === 'array');
	}

	public function set(mixed $value, array $options): string
	{
		return Json::encodeClean($value);
	}

}