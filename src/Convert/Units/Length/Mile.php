<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Length;

use Rovota\Core\Convert\Interfaces\Imperial;
use Rovota\Core\Convert\Units\Length;

final class Mile extends Length implements Imperial
{

	const SYMBOL = 'mi';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 1609.34;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 1609.34;
	}

}