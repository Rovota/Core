<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units;

use Rovota\Core\Convert\Enums\UnitType;
use Rovota\Core\Convert\Units\FuelEconomy\KiloMeterPerLiter;
use Rovota\Core\Convert\Units\FuelEconomy\LiterPerHundredKiloMeters;
use Rovota\Core\Convert\Units\FuelEconomy\MilesPerGallon;

abstract class FuelEconomy extends Unit
{
	const UNIT_TYPE = UnitType::FuelEconomy;

	const BASE_UNIT = KiloMeterPerLiter::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match($identifier) {
			'km/l', 'kilometer per liter' => KiloMeterPerLiter::class,
			'l/100km', 'liter per 100 kilometers' => LiterPerHundredKiloMeters::class,
			'mpg', 'miles per gallon' => MilesPerGallon::class,
			default => null,
		};
	}

}