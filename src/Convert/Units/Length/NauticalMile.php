<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Length;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Units\Length;

final class NauticalMile extends Length implements Metric
{

	const SYMBOL = 'NM';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 0.000539957;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 0.000539957;
	}

}