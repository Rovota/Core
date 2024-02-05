<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units;

use Rovota\Core\Convert\Enums\UnitType;
use Rovota\Core\Convert\Units\Mass\Gram;
use Rovota\Core\Convert\Units\Mass\KiloGram;
use Rovota\Core\Convert\Units\Mass\MilliGram;
use Rovota\Core\Convert\Units\Mass\Ounce;
use Rovota\Core\Convert\Units\Mass\Pound;
use Rovota\Core\Convert\Units\Mass\Tonne;

abstract class Mass extends Unit
{
	const UNIT_TYPE = UnitType::Mass;

	const BASE_UNIT = Gram::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match($identifier) {
			't', 'tonne' => Tonne::class,
			'kg', 'kilogram' => KiloGram::class,
			'g', 'gram' => Gram::class,
			'mg', 'milligram' => MilliGram::class,

			'oz', 'ounce' => Ounce::class,
			'lb', 'pound' => Pound::class,
			default => null,
		};
	}

}