<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Time;

use Rovota\Core\Convert\Units\Time;

final class Day extends Time
{

	const SYMBOL = 'd';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 86400;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 86400;
	}

}