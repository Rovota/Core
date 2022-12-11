<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Exceptions;

use Exception;
use Rovota\Core\Cache\Solutions\CacheMisconfigurationSolution;
use Rovota\Core\Kernel\Interfaces\ProvidesSolution;
use Rovota\Core\Kernel\Interfaces\Solution;

class CacheMisconfigurationException extends Exception implements ProvidesSolution
{

	public function getSolution(): Solution
	{
		return new CacheMisconfigurationSolution();
	}

}