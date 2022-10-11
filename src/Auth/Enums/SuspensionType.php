<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Auth\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum SuspensionType: string
{
	use EnumHelpers;

	case Automatic = 'automatic';
	case Manual = 'manual';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			SuspensionType::Automatic => 'Automatic',
			SuspensionType::Manual => 'Manual',
		};
	}

}