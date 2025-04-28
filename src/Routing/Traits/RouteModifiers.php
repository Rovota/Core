<?php

/**
 * @copyright   Léandro Tijink
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

}