<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum Status: int
{
	use EnumHelpers;

	case Enabled = 1;
	case Disabled = 0;

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Status::Enabled => 'Enabled',
			Status::Disabled => 'Disabled',
		};
	}

}