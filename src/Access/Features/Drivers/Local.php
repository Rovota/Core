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

final class Local extends Feature
{

	protected function resolve(): mixed
	{
		$callback = $this->config->get('definition');

		if (is_bool($callback)) {
			return $callback;
		}

		if (is_string($callback)) {
			$class = new $callback();
			return $class->resolve($this->scope->getData());
		}

		if ($callback instanceof Closure) {
			return $callback($this->scope->getData());
		}

		return null;
	}

}