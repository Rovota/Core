<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Cache\CacheManager;
use Rovota\Core\Cache\Interfaces\CacheInterface;
use Rovota\Core\Support\Str;

final class Cache
{

	protected function __construct()
	{
	}

	// -----------------

	public static function store(string|null $name = null): CacheInterface
	{
		return CacheManager::get($name);
	}

	public static function build(array $options, string|null $name = null): CacheInterface
	{
		return CacheManager::build($name ?? Str::random(20), $options);
	}

	// -----------------

	public static function set(string|int|array $key, mixed $value = null, int|null $retention = null): void
	{
		CacheManager::get()->set($key, $value, $retention);
	}

	public static function forever(string|int|array $key, mixed $value = null): void
	{
		CacheManager::get()->forever($key, $value);
	}

	public static function has(string|int|array $key): bool
	{
		return CacheManager::get()->has($key);
	}

	public static function missing(string|int|array $key): bool
	{
		return CacheManager::get()->missing($key);
	}

	/**
	 * Returns the cached value (or the default), and then removes it from cache.
	 */
	public static function pull(string|int|array $key, mixed $default = null): mixed
	{
		return CacheManager::get()->pull($key, $default);
	}

	public static function get(string|int|array $key, mixed $default = null): mixed
	{
		return CacheManager::get()->get($key, $default);
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

	public static function remove(string|int $key): void
	{
		CacheManager::get()->remove($key);
	}

	public static function flush(): void
	{
		CacheManager::get()->flush();
	}

}