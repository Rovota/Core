<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Interfaces;

interface CacheAdapter
{

	public function all(): array;

	public function has(string $key): bool;

	public function set(string $key, mixed $value, int $retention): void;

	public function increment(string $key, int $step = 1): void;

	public function decrement(string $key, int $step = 1): void;

	public function get(string $key): mixed;

	public function remove(string $key): void;

	// -----------------

	public function flush(): void;

	// -----------------

	public function lastModifiedKey(): string|null;


}