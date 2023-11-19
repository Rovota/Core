<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Interfaces;

use Rovota\Core\Cache\CacheConfig;
use Rovota\Core\Structures\Map;

interface CacheInterface
{

	public function isDefault(): bool;

	// -----------------

	public function name(): string;

	public function config(): CacheConfig;

	// -----------------

	public function all(): Map;

	// -----------------

	public function set(string|int|array $key, mixed $value = null, int|null $retention = null): void;

	public function forever(string|int|array $key, mixed $value = null): void;

	// -----------------

	public function has(string|int|array $key): bool;

	public function missing(string|int|array $key): bool;

	// -----------------

	/**
	 * Returns the cached value (or the default), and then removes it from cache.
	 */
	public function pull(string|int|array $key, mixed $default = null): mixed;

	public function get(string|int|array $key, mixed $default = null): mixed;

	/**
	 * When cached, it'll return the cached value. Otherwise, it'll cache the result of the callback and return that.
	 */
	public function remember(string|int $key, callable $callback, int|null $retention = null): mixed;

	/**
	 * When cached, it'll return the cached value. Otherwise, it'll cache the result of the callback and return that.
	 */
	public function rememberForever(string|int $key, callable $callback): mixed;

	// -----------------

	public function increment(string|int $key, int $step = 1): void;

	public function decrement(string|int $key, int $step = 1): void;

	// -----------------

	public function remove(string|int|array $key): void;

	// -----------------

	public function flush(): void;

	// -----------------

	public function adapter(): CacheAdapter;

	// -----------------

	public function lastModifiedKey(): string|null;

}