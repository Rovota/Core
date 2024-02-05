<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units;

use Rovota\Core\Convert\Enums\UnitType;
use Rovota\Core\Convert\Units\Frequency\GigaHertz;
use Rovota\Core\Convert\Units\Frequency\Hertz;
use Rovota\Core\Convert\Units\Frequency\KiloHertz;
use Rovota\Core\Convert\Units\Frequency\MegaHertz;

abstract class Frequency extends Unit
{
	const UNIT_TYPE = UnitType::Frequency;

	const BASE_UNIT = Hertz::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match($identifier) {
			'hz', 'hertz' => Hertz::class,
			'khz', 'kilohertz' => KiloHertz::class,
			'mhz', 'megahertz' => MegaHertz::class,
			'ghz', 'gigahertz' => GigaHertz::class,
			default => null,
		};
	}

}