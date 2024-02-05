<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units;

use Rovota\Core\Convert\Enums\UnitType;
use Rovota\Core\Convert\Units\Pressure\Bar;
use Rovota\Core\Convert\Units\Pressure\Pascal;
use Rovota\Core\Convert\Units\Pressure\PoundPerSquareInch;

abstract class Pressure extends Unit
{
	const UNIT_TYPE = UnitType::Pressure;

	const BASE_UNIT = Bar::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match($identifier) {
			'bar' => Bar::class,
			'pascal', 'pa' => Pascal::class,
			'psi', 'lbf/in2', 'pound per square inch' => PoundPerSquareInch::class,
			default => null,
		};
	}

}