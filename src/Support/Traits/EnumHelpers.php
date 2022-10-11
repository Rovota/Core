<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Support\Traits;

use Rovota\Core\Support\Arr;

trait EnumHelpers
{

	public function isAny(array $items): bool
	{
		return Arr::containsAny($items, [$this]);
	}

	public function isNone(array $items): bool
	{
		return Arr::containsNone($items, [$this]);
	}

}