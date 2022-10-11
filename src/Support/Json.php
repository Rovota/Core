<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Support;

final class Json
{

	public static function decode(string $data, bool|null $assoc = null, int $depth = 512, int $flags = 0): mixed
	{
		return json_decode($data, $assoc, $depth, $flags) ?? [];
	}

	public static function encode(mixed $data, int $depth = 512, int $flags = 0): string|null
	{
		return json_encode($data, $flags, $depth);
	}

	public static function encodeClean(mixed $data, int $depth = 512): string|null
	{
		return self::encode($data, $depth, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
	}

}