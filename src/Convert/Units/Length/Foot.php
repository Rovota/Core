<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Length;

use Rovota\Core\Convert\Interfaces\Imperial;
use Rovota\Core\Convert\Units\Length;

final class Foot extends Length implements Imperial
{

	const SYMBOL = 'ft';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 3.28084;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 3.28084;
	}

}