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

	public static function isSupported(string $name): bool
	{
		$driver = self::tryFrom($name);

		if ($driver === null) {
			return false;
		}

		return true;
	}

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

	public function description(): string
	{
		return match ($this) {
			Driver::Discord => 'Send the log data to a specified Discord channel.',
			Driver::Monolog => 'Use any logging handlers included with Monolog.',
			Driver::Stack => 'Send a log message to a stack of channels at once.',
			Driver::Stream => 'Store a log message to a file location.',
		};
	}

}