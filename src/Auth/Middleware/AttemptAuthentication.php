<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Auth\Middleware;

use Rovota\Core\Auth\AuthManager;
use Rovota\Core\Http\Request;

class AttemptAuthentication
{
	/**
	 * Attempt to authenticate an identity using the specified authentication provider.
	 */
	public function handle(Request $request): void
	{
		$route = $request->route();

		if ($route !== null) {
			AuthManager::get($route->getAuthProvider())->authenticate();
		}
	}

}