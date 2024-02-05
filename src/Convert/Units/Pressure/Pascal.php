<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Pressure;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Units\Pressure;

final class Pascal extends Pressure implements Metric
{

	const SYMBOL = 'Pa';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 1E5;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 1E5;
	}

}