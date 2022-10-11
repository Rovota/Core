<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Storage\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum MediaType: int
{
	use EnumHelpers;

	case Unknown = 0;
	case Document = 1;
	case Image = 2;
	case Video = 3;
	case Audio = 4;
	case Font = 5;
	case Archive = 6;
	case Raw = 7;

	// -----------------

	public function label(): string
	{
		return match ($this) {
			MediaType::Unknown => 'Unknown',
			MediaType::Document => 'Document',
			MediaType::Image => 'Image',
			MediaType::Video => 'Video',
			MediaType::Audio => 'Audio',
			MediaType::Font => 'Font',
			MediaType::Archive => 'Archive',
			MediaType::Raw => 'Raw',
		};
	}

}