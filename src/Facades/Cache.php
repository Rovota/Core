<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Cache\CacheManager;
use Rovota\Core\Cache\CacheStore;
use Rovota\Core\Support\Text;

final class Cache
{

	protected function __construct()
	{
	}

	// -----------------

	public static function store(string|null $name = null): CacheStore
	{
		return CacheManager::get($name);
	}

	/**
	 * @throws \Rovota\Core\Cache\Exceptions\UnsupportedDriverException
	 */
	public static function build(array $options, string|null $name = null): CacheStore
	{
		return CacheManager::build($name ?? Text::random(20), $options);
	}

	// -----------------

	public static function put(string|int $key, mixed $value, int|null $retention = null): void
	{
		CacheManager::get()->put($key, $value, $retention);
	}

	public static function putMany(array $values, int|null $retention = null): void
	{
		CacheManager::get()->putMany($values, $retention);
	}

	public static function putAllExcept(array $values, string|array $except, int|null $retention = null): void
	{
		CacheManager::get()->putAllExcept($values, $except, $retention);
	}

	public static function forever(string|int $key, mixed $value): void
	{
		CacheManager::get()->forever($key, $value);
	}

	public static function has(string|int $key): bool
	{
		return CacheManager::get()->has($key);
	}

	public static function hasAll(array $keys): bool
	{
		return CacheManager::get()->hasAll($keys);
	}

	public static function missing(string|int $key): bool
	{
		return CacheManager::get()->missing($key);
	}

	/**
	 * Returns the cached value (or the default), and then removes it from cache.
	 */
	public static function pull(string|int $key, mixed $default = null): mixed
	{
		return CacheManager::get()->pull($key, $default);
	}

	/**
	 * Returns multiple cached values, and then removes them from cache.
	 */
	public static function pullMany(array $keys, array $defaults = []): array
	{
		return CacheManager::get()->pullMany($keys, $defaults);
	}

	public static function read(string|int $key, mixed $default = null): mixed
	{
		return CacheManager::get()->read($key, $default);
	}

	public static function readMany(array $keys, array $defaults = []): array
	{
		return CacheManager::get()->readMany($keys, $defaults);
	}

	/**
	 * When cached, it'll return the cached value. Otherwise, it'll cache the result of the callback and return that.
	 */
	public static function remember(string|int $key, callable $callback, int|null $retention = null): mixed
	{
		return CacheManager::get()->remember($key, $callback, $retention);
	}

	/**
	 * When cached, it'll return the cached value. Otherwise, it'll cache the result of the callback and return that.
	 */
	public static function rememberForever(string|int $key, callable $callback): mixed
	{
		return CacheManager::get()->rememberForever($key, $callback);
	}

	public static function increment(string|int $key, int $step = 1): void
	{
		CacheManager::get()->increment($key, $step);
	}

	public static function decrement(string|int $key, int $step = 1): void
	{
		CacheManager::get()->decrement($key, $step);
	}

	public static function forget(string|int $key): void
	{
		CacheManager::get()->forget($key);
	}

	public static function forgetMany(array $keys): void
	{
		CacheManager::get()->forgetMany($keys);
	}

	public static function flush(): void
	{
		CacheManager::get()->flush();
	}

}