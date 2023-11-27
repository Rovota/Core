<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Temperature;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Units\Temperature;

final class Celsius extends Temperature implements Metric
{

	const SYMBOL = '°C';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value + 273.15;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value - 273.15;
	}

}