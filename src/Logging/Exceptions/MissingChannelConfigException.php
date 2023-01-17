<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Logging\Exceptions;

use Exception;
use Rovota\Core\Kernel\Interfaces\ProvidesSolution;
use Rovota\Core\Kernel\Interfaces\Solution;
use Rovota\Core\Logging\Solutions\MissingChannelConfigSolution;

class MissingChannelConfigException extends Exception implements ProvidesSolution
{

	public function getSolution(): Solution
	{
		return new MissingChannelConfigSolution();
	}

}