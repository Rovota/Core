<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Routing;

use Closure;

final class RouteGroup
{

	protected array $attributes = [];

	// -----------------

	public function __construct(string $attribute, mixed $value)
	{
		$this->attributes[$attribute] = $value;
	}

	// -----------------

	public function auth(string $provider): RouteGroup
	{
		$this->attributes['auth'] = $provider;
		return $this;
	}

	public function controller(string $name): RouteGroup
	{
		$this->attributes['controller'] = $name;
		return $this;
	}

	public function middleware(array|string $names): RouteGroup
	{
		$this->attributes['middleware'] = $names;
		return $this;
	}

	public function name(string $value): RouteGroup
	{
		$this->attributes['name'] = $value;
		return $this;
	}

	public function prefix(string $path): RouteGroup
	{
		$this->attributes['prefix'] = $path;
		return $this;
	}

	public function target(mixed $target): RouteGroup
	{
		$this->attributes['target'] = $target;
		return $this;
	}

	public function throttle(string $limiter): RouteGroup
	{
		$this->attributes['limiter'] = $limiter;
		return $this;
	}

	public function where(array|string $parameter, string|null $pattern = null): RouteGroup
	{
		foreach (is_array($parameter) ? $parameter : [$parameter => $pattern] as $parameter => $pattern) {
			$this->attributes['wheres'][$parameter] = $pattern;
		}
		return $this;
	}

	public function whereHash(array|string $parameter, string|int $algorithm): RouteGroup
	{
		$this->where($parameter, '\[a-zA-Z0-9_-]{'.is_string($algorithm) ? hash_length($algorithm) ?? 1 : $algorithm.'}');
		return $this;
	}

	public function whereNumber(array|string $parameter, int|null $length = null): RouteGroup
	{
		$this->where($parameter, '\d'.($length ? '{'.$length.'}' : '+'));
		return $this;
	}

	public function whereSlug(array|string $parameter, int|null $length = null): RouteGroup
	{
		$this->where($parameter, '\[a-zA-Z0-9_-]'.($length ? '{'.$length.'}' : '+'));
		return $this;
	}

	public function withoutMiddleware(array|string $names): RouteGroup
	{
		$this->attributes['without_middleware'] = $names;
		return $this;
	}

	public function withoutThrottling(): RouteGroup
	{
		$this->attributes['limiter'] = null;
		return $this;
	}

	// -----------------

	public function group(Closure $routes): void
	{
		RouteManager::getRouter()->group($routes, $this->attributes);
	}

}