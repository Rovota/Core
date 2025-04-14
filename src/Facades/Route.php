<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Routing\RouteGroup;
use Rovota\Core\Routing\RouteManager;

final class Route
{

	public static function auth(string $provider): RouteGroup
	{
		return RouteManager::getGroup('auth', $provider);
	}

}