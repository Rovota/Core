<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Http\Enums\StatusCode;
use Rovota\Core\Kernel\Application;
use Rovota\Core\Kernel\Registry;
use Rovota\Core\Kernel\Server;
use Rovota\Core\Support\Version;

final class App
{

	protected function __construct()
	{
	}

	// -----------------

	public static function version(): Version
	{
		return Application::$version;
	}

	public static function server(): Server
	{
		return Application::$server;
	}

	public static function registry(): Registry
	{
		return Application::$registry;
	}

	// -----------------

	public static function environment(array|string|null $name = null): string|bool
	{
		return $name === null ? Application::getEnvironment() : Application::isEnvironment($name);
	}

	public static function isLocal(): bool
	{
		return Application::isEnvironment('development');
	}

	public static function isTestable(): bool
	{
		return Application::isEnvironment('testing');
	}

	public static function isStaged(): bool
	{
		return Application::isEnvironment('staging');
	}

	public static function isProduction(): bool
	{
		return Application::isEnvironment('production');
	}

	public static function debugEnabled(): bool
	{
		return Application::debugEnabled();
	}

	public static function loggingEnabled(): bool
	{
		return Application::loggingEnabled();
	}

	// -----------------

	public static function quit(StatusCode $code = StatusCode::InternalServerError): never
	{
		Application::quit($code);
	}

}