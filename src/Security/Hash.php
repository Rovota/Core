<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Security;

use Rovota\Core\Facades\Registry;

final class Hash
{

	public static function make(string $string): string
	{
		return password_hash($string, PASSWORD_DEFAULT, ['cost' => Registry::int('security_hash_cost', 12)]);
	}

	public static function verify(string $string, string $hash): bool
	{
		return password_verify($string, $hash);
	}

	public static function needsRehash(string $hash): bool
	{
		return password_needs_rehash($hash, PASSWORD_DEFAULT, ['cost' => Registry::int('security_hash_cost', 12)]);
	}

}