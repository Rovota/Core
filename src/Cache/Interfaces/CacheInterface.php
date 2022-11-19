<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Interfaces;

interface CacheInterface
{

	public function name(): string;

	public function label(): string;

	public function driver(): string;

	public function option(string $key): mixed;

	// -----------------

	public function put(string|int $key, mixed $value, int|null $retention = null): void;

	public function putMany(array $values, int|null $retention = null): void;

	public function putAllExcept(array $values, string|array $except, int|null $retention = null): void;

	public function forever(string|int $key, mixed $value): void;

	public function has(string|int|array $key): bool;

	public function missing(string|int $key): bool;

	/**
	 * Returns the cached value (or the default), and then removes it from cache.
	 */
	public function pull(string|int $key, mixed $default = null): mixed;

	/**
	 * Returns multiple cached values, and then removes them from cache.
	 */
	public function pullMany(array $keys, array $defaults = []): array;

	public function read(string|int $key, mixed $default = null): mixed;

	public function readMany(array $keys, array $defaults = []): array;

	/**
	 * When cached, it'll return the cached value. Otherwise, it'll cache the result of the callback and return that.
	 */
	public function remember(string|int $key, callable $callback, int|null $retention = null): mixed;

	/**
	 * When cached, it'll return the cached value. Otherwise, it'll cache the result of the callback and return that.
	 */
	public function rememberForever(string|int $key, callable $callback): mixed;

	public function increment(string|int $key, int $step = 1): void;

	public function decrement(string|int $key, int $step = 1): void;

	public function forget(string|int $key): void;

	public function forgetMany(array $keys): void;

	public function flush(): void;

	// -----------------

	public function getPrefix(): string;

	public function setPrefix(string $prefix): void;

	// -----------------

	public function lastModifiedKey(): string|null;

	// -----------------

	public function actions(): array;

}