<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Mass;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Units\Mass;

final class Tonne extends Mass implements Metric
{

	const SYMBOL = 'tonne';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 1000000;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 1000000;
	}

}