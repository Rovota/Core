<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
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