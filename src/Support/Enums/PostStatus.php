<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum PostStatus: int
{
	use EnumHelpers;

	case Draft = 0;
	case Hidden = 1;
	case Unlisted = 2;
	case Protected = 3;
	case Visible = 4;

	// -----------------

	public function label(): string
	{
		return match ($this) {
			PostStatus::Draft => 'Draft',
			PostStatus::Hidden => 'Hidden',
			PostStatus::Unlisted => 'Unlisted',
			PostStatus::Protected => 'Protected',
			PostStatus::Visible => 'Visible',
		};
	}

}