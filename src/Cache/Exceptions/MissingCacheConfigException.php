<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Exceptions;

use Exception;
use Rovota\Core\Kernel\Interfaces\ProvidesSolution;
use Rovota\Core\Kernel\Interfaces\Solution;
use Rovota\Core\Kernel\Solutions\DefaultSolution;

class MissingCacheConfigException extends Exception implements ProvidesSolution
{

	public function getSolution(): Solution
	{
		return new DefaultSolution();
	}

}