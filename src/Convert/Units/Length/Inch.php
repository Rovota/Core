<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Length;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Units\Length;

final class Inch extends Length implements Metric
{

	const SYMBOL = 'ft';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 39.3701;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 39.3701;
	}

}