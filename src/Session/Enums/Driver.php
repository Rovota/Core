<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Session\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum Driver: string
{
	use EnumHelpers;

	case Cookie = 'cookie';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Driver::Cookie => 'Cookie',
		};
	}

}