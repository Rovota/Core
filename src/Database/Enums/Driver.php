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

	case MySQL = 'mysql';
	// case PgSQL = 'pgsql';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Driver::MySQL => 'MySQL',
			// Driver::PgSQL => 'PostgreSQL',
		};
	}

}