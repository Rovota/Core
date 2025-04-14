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

}