<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Time;

use Rovota\Core\Convert\Units\Time;

final class Minute extends Time
{

	const SYMBOL = 'm';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 60;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 60;
	}

}