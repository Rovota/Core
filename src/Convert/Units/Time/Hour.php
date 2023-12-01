<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Time;

use Rovota\Core\Convert\Units\Time;

final class Hour extends Time
{

	const SYMBOL = 'h';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 3600;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 3600;
	}

}