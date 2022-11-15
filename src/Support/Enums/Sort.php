<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum Sort: int
{
	use EnumHelpers;

	case Asc = 4;
	case Desc = 3;
	case Regular = 0;
	case Numeric = 1;
	case String = 2;
	case LocaleString = 5;
	case Natural = 6;

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Sort::Asc => 'Ascending',
			Sort::Desc => 'Descending',
			Sort::Regular => 'Regular',
			Sort::Numeric => 'Numeric',
			Sort::String => 'String',
			Sort::LocaleString => 'LocaleString',
			Sort::Natural => 'Natural',
		};
	}

}