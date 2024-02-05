<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Pressure;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Traits\BaseUnit;
use Rovota\Core\Convert\Units\Pressure;

final class Bar extends Pressure implements Metric
{
	use BaseUnit;

	const SYMBOL = 'bar';

}