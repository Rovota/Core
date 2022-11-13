<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Session\Interfaces;

interface SessionInterface
{

	public function name(): string;

	public function label(): string;

	// -----------------

	public function all(): array;

	public function put(string|int $key, mixed $value): void;

	public function putMany(array $values): void;

	public function putAllExcept(array $values, string|array $except): void;

	public function has(string|int $key): bool;

	public function hasAll(array $keys): bool;

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
	public function remember(string|int $key, callable $callback): mixed;

	public function increment(string|int $key, int $step = 1): void;

	public function decrement(string|int $key, int $step = 1): void;

	public function forget(string|int $key): void;

	public function forgetMany(array $keys): void;

	public function flush(): void;

	// -----------------

	public function flash(string|int $key, mixed $value): void;

	public function flashMany(array $values): void;

	public function reflash(): void;

	public function keep(array|string $keys): void;

}