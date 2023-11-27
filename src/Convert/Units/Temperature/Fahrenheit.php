<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Temperature;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Units\Temperature;

final class Fahrenheit extends Temperature implements Metric
{

	const SYMBOL = '°F';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return ($value - 32) * (5/9) + 273.15;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return ($value - 273.15) * (9/5) + 32;
	}

}