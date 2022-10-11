<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Session\Exceptions;

use Exception;
use Rovota\Core\Kernel\Interfaces\ProvidesSolution;
use Rovota\Core\Kernel\Interfaces\Solution;
use Rovota\Core\Storage\Solutions\UnsupportedDriverSolution;

class UnsupportedDriverException extends Exception implements ProvidesSolution
{

	public function getSolution(): Solution
	{
		return new UnsupportedDriverSolution();
	}

}