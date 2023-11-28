<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\FuelEconomy;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Units\FuelEconomy;

final class LiterPerHundredKiloMeters extends FuelEconomy implements Metric
{

	const SYMBOL = 'l/100km';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return 100/ $value;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return 100 / $value;
	}

}