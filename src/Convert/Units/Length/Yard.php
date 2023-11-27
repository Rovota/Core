<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Length;

use Rovota\Core\Convert\Interfaces\Imperial;
use Rovota\Core\Convert\Units\Length;

final class Yard extends Length implements Imperial
{

	const SYMBOL = 'yd';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 1.09361;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 1.09361;
	}

}