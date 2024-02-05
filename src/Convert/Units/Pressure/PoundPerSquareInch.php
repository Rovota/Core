<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Pressure;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Units\Pressure;

final class PoundPerSquareInch extends Pressure implements Metric
{

	const SYMBOL = 'psi';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 14.5038;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 14.5038;
	}

}