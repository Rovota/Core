<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache;

use RedisException;
use Rovota\Core\Cache\Drivers\APCuStore;
use Rovota\Core\Cache\Drivers\ArrayStore;
use Rovota\Core\Cache\Drivers\RedisStore;
use Rovota\Core\Cache\Exceptions\MissingCacheConfigException;
use Rovota\Core\Cache\Exceptions\UnsupportedDriverException;
use Rovota\Core\Kernel\ExceptionHandler;
use Throwable;

final class CacheManager
{
	/**
	 * @var array<string, CacheStore>
	 */
	protected static array $stores = [];

	protected static string|null $default = null;

	protected static array $config = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 * @throws \Rovota\Core\Cache\Exceptions\UnsupportedDriverException
	 * @throws \Rovota\Core\Cache\Exceptions\MissingCacheConfigException
	 */
	public static function initialize(): void
	{
		$config = require base_path('config/caches.php');
		self::$default = $config['default'];

		foreach ($config['stores'] as $name => $options) {
			self::$config[$name] = $options;
			if ($options['auto_connect'] === false) {
				continue;
			}
			self::connect($name);
		}

		self::configureFallback();

		if (self::$default === null) {
			self::$default = 'fallback';
		}
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Cache\Exceptions\UnsupportedDriverException
	 * @throws \Rovota\Core\Cache\Exceptions\MissingCacheConfigException
	 */
	public static function define(string $name, array $options, bool $connect = false): void
	{
		self::$config[$name] = $options;
		if ($connect) {
			self::connect($name);
		}
	}

	/**
	 * @throws \Rovota\Core\Cache\Exceptions\UnsupportedDriverException
	 * @throws \Rovota\Core\Cache\Exceptions\MissingCacheConfigException
	 */
	public static function connect(string $name): void
	{
		if (!isset(self::$config[$name])) {
			throw new MissingCacheConfigException("There is no config found for a cache named '$name'.");
		}
		self::$stores[$name] = self::build($name, self::$config[$name]);
	}

	/**
	 * @throws \Rovota\Core\Cache\Exceptions\UnsupportedDriverException
	 */
	public static function build(string $name, array $options): CacheStore|null
	{
		return match ($options['driver']) {
			'array' => self::buildArrayStore($name, $options),
			'apcu' => self::buildAPCuStore($name, $options),
			'redis' => self::buildRedisStore($name, $options),
			default => throw new UnsupportedDriverException("The selected driver '{$options['driver']}' is not supported.")
		};
	}

	// -----------------

	public static function isDefined(string $name): bool
	{
		return array_key_exists($name, self::$config);
	}

	public static function isActive(string $name): bool
	{
		return array_key_exists($name, self::$stores);
	}

	// -----------------

	public static function get(string|null $name = null): CacheStore
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
			if (!isset(self::$stores[$name])) {
				$name = 'fallback';
			}
		}
		return self::$stores[$name];
	}

	public static function options(string $name): array
	{
		return self::$config[$name] ?? [];
	}

	/**
	 * @returns array<string, CacheStore>
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
		if (isset(self::$config[$name]) === false) {
			ExceptionHandler::logMessage('warning', "Undefined caches cannot be set as default: '{name}'.", ['name' => $name]);
		}
		self::$default = $name;
	}

	// -----------------

	protected static function configureFallback(): void
	{
		self::$stores['fallback'] = match(true) {
			function_exists('apcu_enabled') && apcu_enabled() => self::buildAPCuStore('fallback', [
				'label' => 'APCu Cache',
				'auto_connect' => true,
				'retention' => 3600,
				'driver' => 'apcu',
			]),
			default => self::buildArrayStore('fallback', [
				'label' => 'Array Cache',
				'auto_connect' => true,
				'driver' => 'array',
			]),
		};
	}

	// -----------------

	protected static function buildArrayStore(string $name, array $options): ArrayStore
	{
		return new ArrayStore($name, $options);
	}

	protected static function buildAPCuStore(string $name, array $options): APCuStore|null
	{
		if (function_exists('apcu_enabled') && apcu_enabled()) {
			if (self::getDefault() === 'array') {
				self::setDefault($name);
			}
			return new APCuStore($name, $options);
		}
		return null;
	}

	protected static function buildRedisStore(string $name, array $options): RedisStore|null
	{
		if (class_exists('\Redis', false)) {
			if (self::getDefault() === 'array') {
				self::setDefault($name);
			}
			try {
				return new RedisStore($name, $options);
			} catch (RedisException $exception) {
				ExceptionHandler::logThrowable($exception, true);
			}
		}
		return null;
	}

}