<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units;

use Rovota\Core\Convert\Enums\UnitType;
use Rovota\Core\Convert\Units\Speed\FootPerSecond;
use Rovota\Core\Convert\Units\Speed\KiloMeterPerHour;
use Rovota\Core\Convert\Units\Speed\Knot;
use Rovota\Core\Convert\Units\Speed\MeterPerSecond;
use Rovota\Core\Convert\Units\Speed\MilePerHour;

abstract class Speed extends Unit
{
	const UNIT_TYPE = UnitType::Speed;

	const BASE_UNIT = MeterPerSecond::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match($identifier) {
			'ms', 'm/s', 'meter per second' => MeterPerSecond::class,
			'kmh', 'km/h', 'kilometer per hour' => KiloMeterPerHour::class,

			'fts', 'ft/s', 'foot per second' => FootPerSecond::class,
			'mph', 'mile per hour' => MilePerHour::class,
			'kn', 'knot' => Knot::class,
			default => null,
		};
	}

}