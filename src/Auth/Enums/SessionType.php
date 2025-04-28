<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Auth\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum SessionType: string
{
	use EnumHelpers;

	case App = 'app';
	case Browser = 'browser';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			SessionType::App => 'App',
			SessionType::Browser => 'Browser',
		};
	}

}