<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Database\Casts;

final class JsonCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return true;
	}

	// -----------------

	public function get(mixed $value, array $options): array
	{
		$json = json_decode($value, isset($options[0]) && $options[0] === 'array');
		if ($json !== null) {
			return is_array($json) ? $json : [$json];
		}
		return [];
	}

	public function set(mixed $value, array $options): string
	{
		return json_encode_clean($value);
	}

}