<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Logging\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum Driver: string
{
	use EnumHelpers;

	case Discord = 'discord';
	case Monolog = 'monolog';
	case Stack = 'stack';
	case Stream = 'stream';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Driver::Discord => 'Discord',
			Driver::Monolog => 'Monolog',
			Driver::Stack => 'Stack',
			Driver::Stream => 'Stream',
		};
	}

}