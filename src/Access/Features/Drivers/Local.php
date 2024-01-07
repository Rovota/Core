<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 *
 * Inspired by Laravel rate limiting.
 */

namespace Rovota\Core\Access\Features\Drivers;

use Closure;
use Rovota\Core\Access\Features\Feature;
use Rovota\Core\Auth\AuthManager;

final class Local extends Feature
{

	protected function resolve(): mixed
	{
		$callback = $this->config->get('callback');

		if ($this->scope === null) {
			$this->scope = AuthManager::activeProvider()?->identity() ?? null;
		}

		if (is_bool($callback)) {
			return $callback;
		}

		if (is_string($callback)) {
			$class = new $callback();
			return $class->resolve($this->scope);
		}

		if ($callback instanceof Closure) {
			return $callback($this->scope);
		}

		return null;
	}

}