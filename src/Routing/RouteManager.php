<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Routing;

use Rovota\Core\Support\Collection;

/**
 * @internal
 */
final class RouteManager
{

	protected static Router $router;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		self::$router = new Router();
	}

	public static function run(): void
	{
		self::$router->run();
	}

	// -----------------

	public static function getRouter(): Router
	{
		return self::$router;
	}

	public static function getGroup(string $attribute, mixed $value): RouteGroup
	{
		return new RouteGroup($attribute, $value);
	}

	public static function registerRoute(array|string $methods, string $path, mixed $target = null): Route
	{
		return self::$router->addRoute($methods, $path, $target);
	}

	public static function registerResource(string $name, string $controller, array $only = []): void
	{
		if (empty($only)) {
			$only = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
		}
		self::registerResourceActions($only, $name, $controller);
	}

	public static function registerApiResource(string $name, string $controller, array $only = []): void
	{
		if (empty($only)) {
			$only = ['index', 'store', 'show', 'update', 'destroy'];
		}
		self::registerResourceActions($only, $name, $controller);
	}

	public static function setFallback(mixed $target = null): Route
	{
		return self::$router->setFallback($target);
	}

	// -----------------

	public static function getCurrentRoute(): Route|null
	{
		return self::$router->getCurrentRoute();
	}

	public static function findRouteByName(string $name): Route|null
	{
		return self::$router->findRouteByName($name);
	}

	public static function findRoutesWithGroupName(string $name): Collection
	{
		return self::$router->findRoutesWithGroupName($name);
	}

	// -----------------

	/**
	 * Inspired by the Laravel Route::resource implementation.
	 */
	protected static function registerResourceActions(array $actions, string $name, string $controller): void
	{
		foreach ($actions as $action) {
			$settings = self::getSettingsUsingAction($action);
			self::$router->addRoute($settings['method'], sprintf($settings['path'], $name), [$controller, $action])
				->name(sprintf('%s.%s', $name, $action))->when($settings['variable'], function (Route $route) {
					$route->where('identifier', '[^\/]+');
				});
		}
	}

	protected static function getSettingsUsingAction(string $action): array
	{
		return match($action) {
			'index' => ['method' => 'GET', 'path' => '%s', 'variable' => false],
			'create' => ['method' => 'GET', 'path' => '%s/create', 'variable' => false],
			'store' => ['method' => 'POST', 'path' => '%s', 'variable' => false],
			'show' => ['method' => 'GET', 'path' => '%s/{identifier}', 'variable' => true],
			'edit' => ['method' => 'GET', 'path' => '%s/{identifier}/edit', 'variable' => true],
			'update' => ['method' => 'PUT', 'path' => '%s/{identifier}', 'variable' => true],
			'destroy' => ['method' => 'DELETE', 'path' => '%s/{identifier}', 'variable' => true],
		};
	}

}