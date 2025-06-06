<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Auth\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum TokenStatus: int
{
	use EnumHelpers;

	case Active = 1;
	case Inactive = 0;

	// -----------------

	public function label(): string
	{
		return match ($this) {
			TokenStatus::Active => 'Active',
			TokenStatus::Inactive => 'Inactive',
		};
	}

}