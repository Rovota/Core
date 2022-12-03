<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Logging\Enums;

use Rovota\Core\Logging\Drivers\Discord;
use Rovota\Core\Logging\Drivers\Monolog;
use Rovota\Core\Logging\Drivers\Stack;
use Rovota\Core\Logging\Drivers\Stream;
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

	public function className(): string
	{
		return match ($this) {
			Driver::Discord => Discord::class,
			Driver::Monolog => Monolog::class,
			Driver::Stack => Stack::class,
			Driver::Stream => Stream::class,
		};
	}

}