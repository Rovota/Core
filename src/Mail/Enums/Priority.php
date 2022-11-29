<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Mail\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum Priority: int
{
	use EnumHelpers;

	case High = 1;
	case Normal = 3;
	case Low = 5;

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Priority::High => 'High',
			Priority::Normal => 'Normal',
			Priority::Low => 'Low',
		};
	}

}