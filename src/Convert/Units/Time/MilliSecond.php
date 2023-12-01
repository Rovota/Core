<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Time;

use Rovota\Core\Convert\Units\Time;

final class MilliSecond extends Time
{

	const SYMBOL = 'ms';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 1000;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 1000;
	}

}