<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Routing;

use BadMethodCallException;
use Rovota\Core\Support\Traits\Errors;

abstract class Controller
{
	use Errors;

	// -----------------

	public function __construct() {}

	public function __call($method, $parameters)
	{
		throw new BadMethodCallException(
			sprintf('Method %s::%s does not exist.', static::class, $method)
		);
	}

}