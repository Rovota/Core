<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Length;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Units\Length;

final class CentiMeter extends Length implements Metric
{

	const SYMBOL = 'cm';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 100;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 100;
	}

}