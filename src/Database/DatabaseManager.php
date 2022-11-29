<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database;

use Rovota\Core\Database\Exceptions\MissingDatabaseConfigException;
use Rovota\Core\Database\Interfaces\ConnectionInterface;
use Rovota\Core\Kernel\ExceptionHandler;
use Throwable;

final class DatabaseManager
{
	/**
	 * @var array<string, ConnectionInterface>
	 */
	protected static array $connections = [];

	protected static string|null $default = null;

	protected static array $config = [];

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
		$config = require base_path('config/databases.php');
		self::$default = $config['default'];

		foreach ($config['connections'] as $name => $options) {
			self::$config[$name] = $options;
			if ($options['auto_connect'] === false) {
				continue;
			}
			self::connect($name);
		}
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Database\Exceptions\MissingDatabaseConfigException
	 */
	public static function define(string $name, array $options, bool $connect = false): void
	{
		self::$config[$name] = $options;
		if ($connect) {
			self::connect($name);
		}
	}

	/**
	 * @throws \Rovota\Core\Database\Exceptions\MissingDatabaseConfigException
	 */
	public static function connect(string $name): void
	{
		if (!isset(self::$config[$name])) {
			throw new MissingDatabaseConfigException("There is no config found for a database named '$name'.");
		}
		self::$connections[$name] = new Connection($name, self::$config[$name]);
	}

	// -----------------

	public static function isDefined(string $name): bool
	{
		return array_key_exists($name, self::$config);
	}

	public static function isActive(string $name): bool
	{
		return array_key_exists($name, self::$connections);
	}

	// -----------------

	public static function get(string|null $name = null): ConnectionInterface
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

	public static function options(string $name): array
	{
		return self::$config[$name] ?? [];
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
		if (isset(self::$config[$name]) === false) {
			ExceptionHandler::logMessage('warning', "Undefined databases cannot be set as default: '{name}'.", ['name' => $name]);
		}
		self::$default = $name;
	}

}