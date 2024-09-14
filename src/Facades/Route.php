<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Routing\RouteGroup;
use Rovota\Core\Routing\RouteManager;

final class Route
{

	protected function __construct()
	{
	}

	// -----------------

	public static function auth(string $provider): RouteGroup
	{
		return RouteManager::getGroup('auth', $provider);
	}

	public static function middleware(array|string $names): RouteGroup
	{
		return RouteManager::getGroup('middleware', $names);
	}

	public static function throttle(string $limiter): RouteGroup
	{
		return RouteManager::getGroup('limiter', $limiter);
	}

	public static function withoutMiddleware(array|string $names): RouteGroup
	{
		return RouteManager::getGroup('without_middleware', $names);
	}

	// -----------------

	public static function resource(string $name, string $controller, array $only = []): void
	{
		RouteManager::registerResource($name, $controller, $only);
	}

	public static function apiResource(string $name, string $controller, array $only = []): void
	{
		RouteManager::registerApiResource($name, $controller, $only);
	}

}