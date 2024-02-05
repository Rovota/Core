<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Speed;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Units\Speed;

final class KiloMeterPerHour extends Speed implements Metric
{

	const SYMBOL = 'km/h';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 3.6;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 3.6;
	}

}