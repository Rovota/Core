<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Addon\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum AddonChannel: string
{
	use EnumHelpers;

	case Alpha = 'alpha';
	case Beta = 'beta';
	case Stable = 'stable';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			AddonChannel::Alpha => 'Alpha',
			AddonChannel::Beta => 'Beta',
			AddonChannel::Stable => 'Stable',
		};
	}

}