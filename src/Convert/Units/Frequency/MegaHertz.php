<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Frequency;

use Rovota\Core\Convert\Interfaces\Metric;
use Rovota\Core\Convert\Units\Frequency;

final class MegaHertz extends Frequency implements Metric
{

	const SYMBOL = 'mhz';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 1E6;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 1E6;
	}

}