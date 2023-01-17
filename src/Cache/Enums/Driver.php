<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum Driver: string
{
	use EnumHelpers;

	case APCu = 'apcu';
	case Array = 'array';
	case Redis = 'redis';

	// -----------------

	public static function isSupported(string $name): bool
	{
		$driver = self::tryFrom($name);

		if ($driver === null) {
			return false;
		}

		if ($driver === Driver::APCu) {
			if (function_exists('apcu_enabled') === false || apcu_enabled() === false) {
				return false;
			}
		}

		if ($driver === Driver::Redis) {
			if (class_exists('\Redis', false) === false) {
				return false;
			}
		}

		return true;
	}

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Driver::APCu => 'APCu',
			Driver::Array => 'Array',
			Driver::Redis => 'Redis',
		};
	}

	public function description(): string
	{
		return match ($this) {
			Driver::APCu => 'Use the APCu extension with a built-in server.',
			Driver::Array => 'Use a regular array that does not remember data.',
			Driver::Redis => 'Use a configured Redis server.',
		};
	}

}