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

abstract class Length extends Unit
{
	const UNIT_TYPE = UnitType::Length;

	const BASE_UNIT = Meter::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match($identifier) {
			'km', 'kilometer' => KiloMeter::class,
			'hm', 'hectometer' => HectoMeter::class,
			'm', 'meter' => Meter::class,
			'dm', 'decimeter' => DeciMeter::class,
			'cm', 'centimeter' => CentiMeter::class,
			'mm', 'millimeter' => MilliMeter::class,
			'μm', 'micrometer' => MicroMeter::class,
			'nm', 'nanometer' => NanoMeter::class,

			'mi', 'mile' => Mile::class,
			'yd', 'yard' => Yard::class,
			'ft', 'foot' => Foot::class,
			'in', 'inch' => Inch::class,
			'NM', 'nautical mile' => NauticalMile::class,
			default => null,
		};
	}

}