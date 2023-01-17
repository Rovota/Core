<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Exceptions;

use Exception;
use Rovota\Core\Database\Solutions\DatabaseMisconfigurationSolution;
use Rovota\Core\Kernel\Interfaces\ProvidesSolution;
use Rovota\Core\Kernel\Interfaces\Solution;

class MissingDatabaseConfigException extends Exception implements ProvidesSolution
{

	public function getSolution(): Solution
	{
		return new DatabaseMisconfigurationSolution();
	}

}