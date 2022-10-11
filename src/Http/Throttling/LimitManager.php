<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Http\Throttling;

use Closure;
use Rovota\Core\Routing\RouteManager;

/**
 * @internal
 */
final class LimitManager
{
	/**
	 * @var array<string, Limiter>
	 */
	protected static array $limiters = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		self::registerDefaultLimits();
	}

	// -----------------

	public static function register(string $name, Closure|Limit $callback): Limiter
	{
		self::$limiters[$name] = new Limiter($name, $callback);
		return self::$limiters[$name];
	}

	// -----------------

	public static function get(string $name): Limiter|null
	{
		return self::$limiters[$name];
	}

	/**
	 * @returns array<string, Limiter>
	 */
	public static function all(): array
	{
		return self::$limiters;
	}

	// -----------------

	public static function activeLimiter(): Limiter|null
	{
		$route = RouteManager::getRouter()->getCurrentRoute();
		if ($route === null) {
			return null;
		}
		return self::$limiters[$route->getLimiter()];
	}

	public static function hitAndTryLimiter(string $name): void
	{
		if (isset(self::$limiters[$name])) {
			self::$limiters[$name]->hitAndTry();
		}
	}

	// -----------------

	protected static function registerDefaultLimits(): void
	{
		self::register('default', function () {
			return Limit::perMinute(200)->withHeaders()->byIP()->response(fn () => 429);
		});
	}

}