<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 *
 * The parameter extraction logic has been derived from bramus/router:
 * @author      Bram(us) Van Damme <bramus@bram.us>
 * @copyright   Copyright (c), 2013 Bram(us) Van Damme
 * @license     MIT public license
 */

namespace Rovota\Core\Routing;

use Closure;

/**
 * @internal
 */
final class Router
{

	public function group(Closure $routes, array $attributes): void
	{
		foreach ($attributes as $key => $value) {
			$this->attributes[$key] = match($key) {
				'middleware' => $this->getMiddlewareAttribute($value),
				'without_middleware' => $this->getWithoutMiddlewareAttribute($value),
				default => $value,
			};
		}
	}

	// -----------------

	protected function getMiddlewareAttribute(array|string $names): array
	{
		$names = is_array($names) ? $names : [$names];
		if (empty($this->attributes['middleware']) === false) {
			return array_merge($this->attributes['middleware'], $names);
		}
		return $names;
	}

	protected function getWithoutMiddlewareAttribute(array|string $names): array
	{
		$names = is_array($names) ? $names : [$names];
		if (empty($this->attributes['without_middleware']) === false) {
			return array_merge($this->attributes['without_middleware'], $names);
		}
		return $names;
	}

}