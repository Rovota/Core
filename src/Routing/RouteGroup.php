<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
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

	public function throttle(string $limiter): RouteGroup
	{
		$this->attributes['limiter'] = $limiter;
		return $this;
	}

	public function withoutThrottling(): RouteGroup
	{
		$this->attributes['limiter'] = null;
		return $this;
	}

}