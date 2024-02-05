<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\FuelEconomy;

use Rovota\Core\Convert\Interfaces\Imperial;
use Rovota\Core\Convert\Units\FuelEconomy;

final class MilesPerGallonImperial extends FuelEconomy implements Imperial
{

	const SYMBOL = 'mpg';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 2.82481;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 2.82481;
	}

}