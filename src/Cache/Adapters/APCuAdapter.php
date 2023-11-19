<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Adapters;

use Rovota\Core\Cache\Interfaces\CacheAdapter;
use Rovota\Core\Support\Config;

class APCuAdapter implements CacheAdapter
{

	protected string|null $scope = null;

	protected string|null $last_modified_key = null;

	// -----------------

	public function __construct(Config $parameters)
	{
		$this->scope = $parameters->get('scope');
	}

	// -----------------

	public function all(): array
	{
		return [];
	}

	public function has(string $key): bool
	{
		$key = $this->getFullKey($key);
		return apcu_exists($key);
	}

	public function set(string $key, mixed $value, int $retention): void
	{
		$key = $this->getFullKey($key);
		$this->last_modified_key = $key;
		apcu_store($key, $value, $retention);
	}

	public function increment(string $key, int $step = 1): void
	{
		$key = $this->getFullKey($key);
		$this->last_modified_key = $key;
		apcu_inc($key, max($step, 0));
	}

	public function decrement(string $key, int $step = 1): void
	{
		$key = $this->getFullKey($key);
		$this->last_modified_key = $key;
		apcu_dec($key, max($step, 0));
	}

	public function get(string $key): mixed
	{
		$key = $this->getFullKey($key);
		return apcu_exists($key) ? apcu_fetch($key) : null;
	}

	public function remove(string $key): void
	{
		$key = $this->getFullKey($key);
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

	// -----------------

	protected function getFullKey(string|int $key): string
	{
		if ($this->scope === null || mb_strlen($this->scope) === 0) {
			return (string) $key;
		}
		return $this->scope.':'.$key;
	}

}