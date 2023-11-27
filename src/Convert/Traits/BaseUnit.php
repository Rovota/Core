<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Traits;

use Rovota\Core\Convert\Units\Unit;

trait BaseUnit
{

	protected function toBaseUnit(): Unit
	{
		if (static::class === self::class) {
			return $this;
		}
		return parent::toBaseUnit();
	}

}