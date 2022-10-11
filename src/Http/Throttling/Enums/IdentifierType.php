<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Http\Throttling\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum IdentifierType: string
{
	use EnumHelpers;

	case Global = 'global';
	case Custom = 'custom';
	case IP = 'ip';
	case Token = 'token';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			IdentifierType::Global => 'Global',
			IdentifierType::Custom => 'Custom',
			IdentifierType::IP => 'IP Address',
			IdentifierType::Token => 'Token',
		};
	}

}