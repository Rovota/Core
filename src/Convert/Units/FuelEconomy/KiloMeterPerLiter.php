<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\FuelEconomy;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Traits\BaseUnit;
use Rovota\Core\Convert\Units\FuelEconomy;

final class KiloMeterPerLiter extends FuelEconomy implements Metric
{
	use BaseUnit;

	const SYMBOL = 'km/l';

}