<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Routing;

final class RouteGroup
{

	public function auth(string $provider): RouteGroup
	{
		$this->attributes['auth'] = $provider;
		return $this;
	}

}