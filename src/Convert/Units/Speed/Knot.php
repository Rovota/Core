<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Speed;

use Rovota\Core\Convert\Interfaces\Imperial;
use Rovota\Core\Convert\Units\Speed;

final class Knot extends Speed implements Imperial
{

	const SYMBOL = 'kn';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value / 1.94384;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value * 1.94384;
	}

}