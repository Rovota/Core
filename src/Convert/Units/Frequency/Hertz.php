<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Frequency;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Traits\BaseUnit;
use Rovota\Core\Convert\Units\Frequency;

final class Hertz extends Frequency implements Metric
{
	use BaseUnit;

	const SYMBOL = 'hz';

}