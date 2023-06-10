<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Adapters;

use Rovota\Core\Cache\Interfaces\CacheAdapter;

class APCuAdapter implements CacheAdapter
{

	protected string|null $last_modified_key = null;

	// -----------------

	public function all(): array
	{
		return [];
	}

	public function has(string $key): bool
	{
		return apcu_exists($key);
	}

	public function set(string $key, mixed $value, int $retention): void
	{
		$this->last_modified_key = $key;
		apcu_store($key, $value, $retention);
	}

	public function increment(string $key, int $step = 1): void
	{
		$this->last_modified_key = $key;
		apcu_inc($key, max($step, 0));
	}

	public function decrement(string $key, int $step = 1): void
	{
		$this->last_modified_key = $key;
		apcu_dec($key, max($step, 0));
	}

	public function get(string $key): mixed
	{
		return apcu_exists($key) ? apcu_fetch($key) : null;
	}

	public function remove(string $key): void
	{
		$this->last_modified_key = $key;
		apcu_delete($key);
	}

	// -----------------

	public function flush(): void
	{
		apcu_clear_cache();
	}

	// -----------------

	public function lastModifiedKey(): string|null
	{
		return $this->last_modified_key;
	}

}