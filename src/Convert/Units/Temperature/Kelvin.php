<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Temperature;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Traits\BaseUnit;
use Rovota\Core\Convert\Units\Temperature;

final class Kelvin extends Temperature implements Metric
{
	use BaseUnit;

	const SYMBOL = 'K';

}