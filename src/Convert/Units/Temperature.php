<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units;

use Rovota\Core\Convert\Enums\UnitType;
use Rovota\Core\Convert\Units\Length\CentiMeter;
use Rovota\Core\Convert\Units\Length\DeciMeter;
use Rovota\Core\Convert\Units\Length\Foot;
use Rovota\Core\Convert\Units\Length\HectoMeter;
use Rovota\Core\Convert\Units\Length\Inch;
use Rovota\Core\Convert\Units\Length\KiloMeter;
use Rovota\Core\Convert\Units\Length\Meter;
use Rovota\Core\Convert\Units\Length\MicroMeter;
use Rovota\Core\Convert\Units\Length\Mile;
use Rovota\Core\Convert\Units\Length\MilliMeter;
use Rovota\Core\Convert\Units\Length\NanoMeter;
use Rovota\Core\Convert\Units\Length\NauticalMile;
use Rovota\Core\Convert\Units\Length\Yard;
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