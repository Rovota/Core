<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Addon\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum AddonType: string
{
	use EnumHelpers;

	case Library = 'library';
	case Module = 'module';
	case Theme = 'theme';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			AddonType::Library => 'Library',
			AddonType::Module => 'Module',
			AddonType::Theme => 'Theme',
		};
	}

}