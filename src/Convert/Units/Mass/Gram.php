<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Mass;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Traits\BaseUnit;
use Rovota\Core\Convert\Units\Mass;

final class Gram extends Mass implements Metric
{
	use BaseUnit;

	const SYMBOL = 'g';

}