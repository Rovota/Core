<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum Driver: string
{
	use EnumHelpers;

	case MySql = 'mysql';
	case PostgreSql = 'pgsql';

	// -----------------

	public static function isSupported(string $name): bool
	{
		$driver = self::tryFrom($name);

		if ($driver === null) {
			return false;
		}

		return true;
	}

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Driver::MySql => 'MySQL',
			Driver::PostgreSql => 'PostgreSQL',
		};
	}

	public function description(): string
	{
		return match ($this) {
			Driver::MySql => 'Connect to a database using MySQL or MariaDB.',
			Driver::PostgreSql => 'Connect to a database using PostgreSQL.',
		};
	}

}