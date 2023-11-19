<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Adapters;

use Rovota\Core\Cache\Interfaces\CacheAdapter;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Config;

class PhpArrayAdapter implements CacheAdapter
{

	protected Bucket $storage;

	protected string|null $scope = null;

	protected string|null $last_modified_key = null;

	// -----------------

	public function __construct(Config $parameters)
	{
		$this->storage = new Bucket();

		$this->scope = $parameters->get('scope');
	}

	// -----------------

	public function all(): array
	{
		return $this->storage->toArray();
	}

	public function has(string $key): bool
	{
		$key = $this->getFullKey($key);
		return $this->storage->has($key);
	}

	public function set(string $key, mixed $value, int $retention): void
	{
		$key = $this->getFullKey($key);
		$this->last_modified_key = $key;
		$this->storage->set($key, $value);
	}

	public function increment(string $key, int $step = 1): void
	{
		$key = $this->getFullKey($key);
		$this->last_modified_key = $key;
		$this->storage->increment($key, $step);
	}

	public function decrement(string $key, int $step = 1): void
	{
		$key = $this->getFullKey($key);
		$this->last_modified_key = $key;
		$this->storage->increment($key, $step);
	}

	public function get(string $key): mixed
	{
		$key = $this->getFullKey($key);
		return $this->storage->get($key);
	}

	public function remove(string $key): void
	{
		$key = $this->getFullKey($key);
		$this->last_modified_key = $key;
		$this->storage->remove($key);
	}

	// -----------------

	public function flush(): void
	{
		$this->storage->flush();
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