<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache;

use Rovota\Core\Cache\Drivers\APCu;
use Rovota\Core\Cache\Drivers\PhpArray;
use Rovota\Core\Cache\Drivers\Redis;
use Rovota\Core\Cache\Enums\Driver;
use Rovota\Core\Cache\Exceptions\CacheMisconfigurationException;
use Rovota\Core\Cache\Exceptions\MissingCacheConfigException;
use Rovota\Core\Cache\Exceptions\UnsupportedDriverException;
use Rovota\Core\Cache\Interfaces\CacheInterface;
use Rovota\Core\Kernel\ExceptionHandler;
use Throwable;

final class CacheManager
{
	/**
	 * @var array<string, CacheInterface>
	 */
	protected static array $stores = [];

	protected static array $configs = [];

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
		$file = require base_path('config/caching.php');

		foreach ($file['stores'] as $name => $config) {
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
			ExceptionHandler::addThrowable(new MissingCacheConfigException("There is no config found for a cache named '$name'."));
		}
		self::$stores[$name] = self::build($name, self::$configs[$name]);
	}

	public static function build(string $name, array $config): CacheInterface|null
	{
		$config = new CacheConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::addThrowable(new UnsupportedDriverException("The selected driver '{$config->get('driver')}' is not supported."));
			return null;
		}

		if ($config->isValid() === false) {
			ExceptionHandler::addThrowable(new CacheMisconfigurationException("The cache '$name' cannot be used due to a configuration issue."));
			return null;
		}

		try {
			return match ($config->driver) {
				Driver::APCu => new APCu($name, $config),
				Driver::Array => new PhpArray($name, $config),
				Driver::Redis => new Redis($name, $config),
				default => null,
			};
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
			return null;
		}
	}

	// -----------------

	public static function isDefined(string $name): bool
	{
		return array_key_exists($name, self::$configs);
	}

	public static function isConnected(string $name): bool
	{
		return array_key_exists($name, self::$stores);
	}

	// -----------------

	public static function get(string|null $name = null): CacheInterface|null
	{
		if ($name === null) {
			$name = self::$default;
		}
		if (!isset(self::$stores[$name])) {
			try {
				self::connect($name);
			} catch (Throwable $throwable) {
				ExceptionHandler::addThrowable($throwable, true);
				exit;
			}
		}
		return self::$stores[$name];
	}

	/**
	 * @returns array<string, CacheInterface>
	 */
	public static function all(): array
	{
		return self::$stores;
	}

	// -----------------

	public static function getDefault(): string|null
	{
		return self::$default;
	}

	public static function setDefault(string $name): void
	{
		if (isset(self::$configs[$name]) === false) {
			ExceptionHandler::addThrowable(new MissingCacheConfigException("Undefined caches cannot be set as default: '$name'."));
		}
		self::$default = $name;
	}

}