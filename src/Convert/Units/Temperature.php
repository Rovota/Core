<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units;

use Rovota\Core\Convert\Enums\UnitType;
use Rovota\Core\Convert\Units\Temperature\Celsius;
use Rovota\Core\Convert\Units\Temperature\Fahrenheit;
use Rovota\Core\Convert\Units\Temperature\Kelvin;

abstract class Temperature extends Unit
{
	const UNIT_TYPE = UnitType::Temperature;

	const BASE_UNIT = Kelvin::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match($identifier) {
			'K', 'Kelvin' => Kelvin::class,
			'°F', 'F', 'fahrenheit' => Fahrenheit::class,
			'°C', 'C', 'celsius' => Celsius::class,
			default => null,
		};
	}

}