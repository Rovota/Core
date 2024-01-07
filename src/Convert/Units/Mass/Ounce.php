<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units\Mass;

use Rovota\Core\Convert\Interfaces\Imperial;
use Rovota\Core\Convert\Units\Mass;

final class Ounce extends Mass implements Imperial
{

	const SYMBOL = 'oz';

	// -----------------

	protected function toBaseValue(float|int $value): float|int
	{
		return $value * 28.3495;
	}

	protected function fromBaseValue(float|int $value): float|int
	{
		return $value / 28.3495;
	}

}