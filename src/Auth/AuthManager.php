<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Auth;

use Rovota\Core\Auth\Interfaces\AuthProvider;
use Rovota\Core\Auth\Providers\SessionProvider;
use Rovota\Core\Auth\Providers\TokenProvider;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Routing\RouteManager;

final class AuthManager
{

	/**
	 * @var array<string, AuthProvider>
	 */
	protected static array $providers = [];

	protected static string|null $default = null;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 */
	public static function initialize(): void
	{
		self::registerDefaultProviders();
		self::$default = 'session';
	}

	// -----------------

	public static function register(string $name, AuthProvider $provider): void
	{
		self::$providers[$name] = $provider;
	}

	// -----------------

	public static function get(string|null $name = null): AuthProvider
	{
		if ($name === null) {
			$name = self::$default;
		}
		return self::$providers[$name];
	}

	// -----------------

	public static function activeProvider(): AuthProvider|null
	{
		$route = RouteManager::getRouter()->getCurrentRoute();
		if ($route === null) {
			return null;
		}
		return self::$providers[$route->getAuthProvider()];
	}

	// -----------------

	public static function getDefault(): string
	{
		return self::$default;
	}

	public static function setDefault(string $name): void
	{
		if (isset(self::$providers[$name]) === false) {
			ExceptionHandler::logMessage('warning', "Undefined providers cannot be set as default: '{name}'.", ['name' => $name]);
		}
		self::$default = $name;
	}

	// -----------------

	protected static function registerDefaultProviders(): void
	{
		self::register('session', new SessionProvider());
		self::register('token', new TokenProvider());
	}

}