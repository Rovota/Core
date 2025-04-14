<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Routing;

use Rovota\Core\Auth\AuthManager;

final class Route
{

	// Optional
	protected string|null $auth = null;

	// -----------------

	public function getAuthProvider(): string
	{
		return $this->auth ?? AuthManager::getDefault();
	}

}