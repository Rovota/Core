<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Routing\RouteGroup;
use Rovota\Core\Routing\RouteManager;
use Rovota\Core\Routing\Route as RouteObject;
use Rovota\Core\Structures\Bucket;

final class Route
{

	protected function __construct()
	{
	}

	// -----------------
	
	public static function fallback(mixed $target = null): RouteObject
	{
		return RouteManager::setFallback($target);
	}

	// -----------------

	public static function current(): RouteObject|null
	{
		return RouteManager::getCurrentRoute();
	}

	public static function currentName(): string|null
	{
		return RouteManager::getCurrentRoute()?->getName();
	}

	// -----------------

	public static function findByName(string $name): RouteObject|null
	{
		return RouteManager::findRouteByName($name);
	}

	public static function allWithGroupName(string $name): Bucket
	{
		return RouteManager::findRoutesWithGroupName($name);
	}

	// -----------------

	public static function auth(string $provider): RouteGroup
	{
		return RouteManager::getGroup('auth', $provider);
	}

	public static function controller(string $name): RouteGroup
	{
		return RouteManager::getGroup('controller', $name);
	}

	public static function middleware(array|string $names): RouteGroup
	{
		return RouteManager::getGroup('middleware', $names);
	}

	public static function name(string $value): RouteGroup
	{
		return RouteManager::getGroup('name', $value);
	}

	public static function prefix(string $path): RouteGroup
	{
		return RouteManager::getGroup('prefix', $path);
	}

	public static function target(mixed $target): RouteGroup
	{
		return RouteManager::getGroup('target', $target);
	}

	public static function where(array|string $parameter, string|null $pattern = null): RouteGroup
	{
		if (is_string($parameter)) {
			$parameter = [$parameter => $pattern];
		}
		return RouteManager::getGroup('wheres', $parameter);
	}

	public static function whereHash(array|string $parameter, string|int $algorithm): RouteGroup
	{
		if (is_string($parameter)) {
			$parameter = [$parameter => '[a-zA-Z0-9_-]{'.(is_string($algorithm) ? hash_length($algorithm) ?? 1 : $algorithm).'}'];
		}
		return RouteManager::getGroup('wheres', $parameter);
	}

	public static function whereNumber(array|string $parameter, int|null $length = null): RouteGroup
	{
		if (is_string($parameter)) {
			$parameter = [$parameter => '\d'.($length ? '{'.$length.'}' : '+')];
		}
		return RouteManager::getGroup('wheres', $parameter);
	}

	public static function whereSlug(array|string $parameter, int|null $length = null): RouteGroup
	{
		if (is_string($parameter)) {
			$parameter = [$parameter => '[a-zA-Z0-9_-]'.($length ? '{'.$length.'}' : '+')];
		}
		return RouteManager::getGroup('wheres', $parameter);
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

	// -----------------

	public static function match(array|string $methods, string $path, mixed $target = null): RouteObject
	{
		return RouteManager::registerRoute($methods, $path, $target);
	}

	public static function all(string $path, mixed $target = null): RouteObject
	{
		return RouteManager::registerRoute(['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH', 'HEAD'], $path, $target);
	}

	public static function get(string $path, mixed $target = null): RouteObject
	{
		return RouteManager::registerRoute(['GET'], $path, $target);
	}

	public static function post(string $path, mixed $target = null): RouteObject
	{
		return RouteManager::registerRoute(['POST'], $path, $target);
	}

	public static function put(string $path, mixed $target = null): RouteObject
	{
		return RouteManager::registerRoute(['PUT'], $path, $target);
	}

	public static function delete(string $path, mixed $target = null): RouteObject
	{
		return RouteManager::registerRoute(['DELETE'], $path, $target);
	}

	public static function options(string $path, mixed $target = null): RouteObject
	{
		return RouteManager::registerRoute(['OPTIONS'], $path, $target);
	}

	public static function patch(string $path, mixed $target = null): RouteObject
	{
		return RouteManager::registerRoute(['PATCH'], $path, $target);
	}

	public static function head(string $path, mixed $target = null): RouteObject
	{
		return RouteManager::registerRoute(['HEAD'], $path, $target);
	}

}