<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\FuelEconomy;

use Rovota\Core\Convert\Interfaces\USC;
use Rovota\Core\Convert\Units\FuelEconomy;

final class MilesPerGallon extends FuelEconomy implements USC
{

	const SYMBOL = 'mpg';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 2.352;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 2.352;
	}

}