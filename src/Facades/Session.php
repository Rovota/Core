<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Session\Interfaces\SessionInterface;
use Rovota\Core\Session\SessionManager;

final class Session
{

	protected function __construct()
	{
	}

	// -----------------

	public static function handler(string|null $name = null): SessionInterface
	{
		return SessionManager::get($name);
	}

	// -----------------

	public static function all(): array
	{
		return SessionManager::get()->all();
	}

	public static function put(string|int $key, mixed $value): void
	{
		SessionManager::get()->put($key, $value);
	}

	public static function putMany(array $values): void
	{
		SessionManager::get()->putMany($values);
	}

	public static function putAllExcept(array $values, string|array $except): void
	{
		SessionManager::get()->putAllExcept($values, $except);
	}

	public static function has(string|int $key): bool
	{
		return SessionManager::get()->has($key);
	}

	public static function hasAll(array $keys): bool
	{
		return SessionManager::get()->hasAll($keys);
	}

	public static function missing(string|int $key): bool
	{
		return SessionManager::get()->missing($key);
	}

	/**
	 * Returns the cached value (or the default), and then removes it from cache.
	 */
	public static function pull(string|int $key, mixed $default = null): mixed
	{
		return SessionManager::get()->pull($key, $default);
	}

	/**
	 * Returns multiple cached values, and then removes them from cache.
	 */
	public static function pullMany(array $keys, array $defaults = []): array
	{
		return SessionManager::get()->pullMany($keys, $defaults);
	}

	public static function read(string|int $key, mixed $default = null): mixed
	{
		return SessionManager::get()->read($key, $default);
	}

	public static function readMany(array $keys, array $defaults = []): array
	{
		return SessionManager::get()->readMany($keys, $defaults);
	}

	/**
	 * When cached, it'll return the cached value. Otherwise, it'll cache the result of the callback and return that.
	 */
	public static function remember(string|int $key, callable $callback): mixed
	{
		return SessionManager::get()->remember($key, $callback);
	}

	/**
	 * When cached, it'll return the cached value. Otherwise, it'll cache the result of the callback and return that.
	 */
	public static function increment(string|int $key, int $step = 1): void
	{
		SessionManager::get()->increment($key, $step);
	}

	public static function decrement(string|int $key, int $step = 1): void
	{
		SessionManager::get()->decrement($key, $step);
	}

	public static function forget(string|int $key): void
	{
		SessionManager::get()->forget($key);
	}

	public static function forgetMany(array $keys): void
	{
		SessionManager::get()->forgetMany($keys);
	}

	public static function flush(): void
	{
		SessionManager::get()->flush();
	}

	// -----------------

	public static function flash(string|int $key, mixed $value): void
	{
		SessionManager::get()->flash($key, $value);
	}

	public static function flashMany(array $values): void
	{
		SessionManager::get()->flashMany($values);
	}

	public static function reflash(): void
	{
		SessionManager::get()->reflash();
	}

	public static function keep(array|string $keys): void
	{
		SessionManager::get()->keep($keys);
	}

}