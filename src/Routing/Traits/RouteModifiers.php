<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Routing\Traits;

trait RouteModifiers
{

	public function auth(string $provider): static
	{
		$this->auth = $provider;
		return $this;
	}

	// -----------------

	public function throttle(string $limiter): static
	{
		$this->limiter = $limiter;
		return $this;
	}

	public function withoutThrottling(): static
	{
		$this->limiter = null;
		return $this;
	}

}