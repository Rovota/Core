<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support\Traits;

use Rovota\Core\Support\Helpers\Arr;

trait EnumHelpers
{

	public function isAny(array $items): bool
	{
		return Arr::containsAny($items, [$this]);
	}

	public function isNone(array $items): bool
	{
		return Arr::containsAny($items, [$this]) === false;
	}

}