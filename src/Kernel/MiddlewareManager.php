<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Kernel;

use Rovota\Core\Auth\Middleware\AttemptAuthentication;
use Rovota\Core\Auth\Middleware\CsrfProtection;
use Rovota\Core\Http\RequestManager;
use Rovota\Core\Localization\Middleware\DetermineLanguage;

/**
 * @internal
 */
final class MiddlewareManager
{

	protected static array $middleware = [];

	protected static array $global = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		self::registerDefaultMiddleware();
	}

	// -----------------

	public static function register(string $name, string $class, bool $global = false): void
	{
		self::$middleware[$name] = [$class, 'handle'];
		if ($global) {
			self::$global[] = $name;
		}
	}

	public static function global(array|string $names): void
	{
		foreach (is_array($names) ? $names : [$names] as $name) {
			if (isset(self::$middleware[$name])) {
				self::$global[] = $name;
			}
		}
	}

	// -----------------

	public static function execute(array $names, array $without = []): void
	{
		foreach (self::$global as $name) {
			if (!in_array($name, $without)) {
				Resolver::invoke(self::$middleware[$name], [RequestManager::getRequest()]);
			}
		}

		foreach ($names as $name) {
			if (isset(self::$middleware[$name]) && !in_array($name, $without)) {
				Resolver::invoke(self::$middleware[$name], [RequestManager::getRequest()]);
			}
		}
	}

	// -----------------
	
	protected static function registerDefaultMiddleware(): void
	{
		self::register('attempt_auth', AttemptAuthentication::class, true);
		self::register('determine_language', DetermineLanguage::class, true);
		self::register('csrf_protection', CsrfProtection::class, true);
	}

}