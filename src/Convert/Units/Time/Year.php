<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Time;

use Rovota\Core\Convert\Units\Time;

final class Year extends Time
{

	const SYMBOL = 'y';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 31556952;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 31556952;
	}

}