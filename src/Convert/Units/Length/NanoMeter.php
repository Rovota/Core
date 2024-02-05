<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Length;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Units\Length;

final class NanoMeter extends Length implements Metric
{

	const SYMBOL = 'nm';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 1E9;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 1E9;
	}

}