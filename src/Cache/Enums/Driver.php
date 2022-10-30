<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
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

	public function label(): string
	{
		return match ($this) {
			Driver::APCu => 'APCu',
			Driver::Array => 'Custom',
			Driver::Redis => 'Redis',
		};
	}

}