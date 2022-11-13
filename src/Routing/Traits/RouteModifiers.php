<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Routing\Traits;

use Rovota\Core\Routing\Controller;

trait RouteModifiers
{

	public function name(string $name): static
	{
		$this->name = $this->name === null ? $name : sprintf('%s.%s', $this->name, $name);
		return $this;
	}

	public function target(mixed $target): static
	{
		$this->target = is_string($target) ? [$this->controller ?? Controller::class, $target] : $target;
		return $this;
	}

	// -----------------

	public function auth(string $provider): static
	{
		$this->auth = $provider;
		return $this;
	}

	// -----------------

	public function middleware(array|string $names): static
	{
		$this->middleware = array_merge($this->middleware, is_array($names) ? $names : [$names]);
		return $this;
	}

	public function withoutMiddleware(array|string $names): static
	{
		$this->without_middleware = array_merge($this->without_middleware, is_array($names) ? $names : [$names]);
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

	// -----------------

	public function where(array|string $parameter, string|null $pattern = null): static
	{
		foreach (is_array($parameter) ? $parameter : [$parameter => $pattern] as $parameter => $pattern) {
			$this->wheres[$parameter] = $pattern;
		}
		return $this;
	}

	public function whereHash(array|string $parameter, string|int $algorithm): static
	{
		$this->where($parameter, '\[a-zA-Z0-9_-]{'.is_string($algorithm) ? hash_length($algorithm) ?? 1 : $algorithm.'}');
		return $this;
	}

	public function whereNumber(array|string $parameter, int|null $length = null): static
	{
		$this->where($parameter, '\d'.($length ? '{'.$length.'}' : '+'));
		return $this;
	}

	public function whereSlug(array|string $parameter, int|null $length = null): static
	{
		$this->where($parameter, '\[a-zA-Z0-9_-]'.($length ? '{'.$length.'}' : '+'));
		return $this;
	}

}