<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Routing;

use Rovota\Core\Auth\AuthManager;

final class Route
{

	// Optional
	protected string|null $auth = null;
	protected array $middleware = [];
	protected string|null $limiter = null;
	protected array $without_middleware = [];

	// -----------------

	public function getAuthProvider(): string
	{
		return $this->auth ?? AuthManager::getDefault();
	}

	public function getMiddleware(): array
	{
		return $this->middleware;
	}

	public function getLimiter(): string|null
	{
		return $this->limiter;
	}

	public function getWithoutMiddleware(): array
	{
		return $this->without_middleware;
	}

	// -----------------

	public function getMethods(): array
	{
		return $this->methods;
	}

	public function getPath(): string
	{
		return $this->path;
	}

	// -----------------

	public function getUrl(array $params = [], array $query = []): string|null
	{
		$builder = new UrlBuilder();
		return (string) $builder->route($this->name, $params, $query);
	}

	// -----------------

	public function hasLimiter(): bool
	{
		return $this->limiter !== null;
	}

}