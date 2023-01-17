<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database;

use Rovota\Core\Database\Drivers\MySql;
use Rovota\Core\Database\Drivers\PostgreSql;
use Rovota\Core\Database\Enums\Driver;
use Rovota\Core\Database\Exceptions\DatabaseMisconfigurationException;
use Rovota\Core\Database\Exceptions\MissingDatabaseConfigException;
use Rovota\Core\Database\Exceptions\UnsupportedDriverException;
use Rovota\Core\Database\Interfaces\ConnectionInterface;
use Rovota\Core\Kernel\ExceptionHandler;
use Throwable;

final class DatabaseManager
{
	/**
	 * @var array<string, ConnectionInterface>
	 */
	protected static array $connections = [];

	protected static array $configs = [];

	protected static string|null $default = null;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 * @throws \Rovota\Core\Database\Exceptions\MissingDatabaseConfigException
	 */
	public static function initialize(): void
	{
		$file = require base_path('config/databases.php');

		foreach ($file['connections'] as $name => $config) {
			self::define($name, $config);
		}

		self::setDefault($file['default']);
	}

	// -----------------

	public static function define(string $name, array $config): void
	{
		self::$configs[$name] = $config;

		if ($config['auto_connect']) {
			self::connect($name);
		}
	}

	public static function connect(string $name): void
	{
		if (isset(self::$configs[$name]) === false) {
			ExceptionHandler::addThrowable(new MissingDatabaseConfigException("There is no config found for a database named '$name'."));
		}
		self::$connections[$name] = self::build($name, self::$configs[$name]);
	}

	public static function build(string $name, array $config): ConnectionInterface|null
	{
		$config = new ConnectionConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::addThrowable(new UnsupportedDriverException("The selected driver '{$config->get('driver')}' is not supported."));
			return null;
		}

		if ($config->isValid() === false) {
			ExceptionHandler::addThrowable(new DatabaseMisconfigurationException("The database '$name' cannot be used due to a configuration issue."));
			return null;
		}

		return match ($config->driver) {
			Driver::MySql => new MySql($name, $config),
			Driver::PostgreSql => new PostgreSql($name, $config),
			default => null,
		};
	}

	// -----------------

	public static function isDefined(string $name): bool
	{
		return array_key_exists($name, self::$configs);
	}

	public static function isConnected(string $name): bool
	{
		return array_key_exists($name, self::$connections);
	}

	// -----------------

	public static function get(string|null $name = null): ConnectionInterface|null
	{
		if ($name === null) {
			$name = self::$default;
		}
		if (!isset(self::$connections[$name])) {
			try {
				self::connect($name);
			} catch (Throwable $throwable) {
				ExceptionHandler::addThrowable($throwable, true);
				exit;
			}
		}
		return self::$connections[$name];
	}

	/**
	 * @returns array<string, ConnectionInterface>
	 */
	public static function all(): array
	{
		return self::$connections;
	}

	// -----------------

	public static function getDefault(): string|null
	{
		return self::$default;
	}

	public static function setDefault(string $name): void
	{
		if (isset(self::$configs[$name]) === false) {
			ExceptionHandler::addThrowable(new MissingDatabaseConfigException("Undefined databases cannot be set as default: '$name'."));
		}
		self::$default = $name;
	}

}