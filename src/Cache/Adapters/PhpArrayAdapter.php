<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Adapters;

use Rovota\Core\Cache\Interfaces\CacheAdapter;
use Rovota\Core\Structures\Bucket;

class PhpArrayAdapter implements CacheAdapter
{

	protected Bucket $storage;

	protected string|null $last_modified_key = null;

	// -----------------

	public function __construct()
	{
		$this->storage = new Bucket();
	}

	// -----------------

	public function all(): array
	{
		return $this->storage->toArray();
	}

	public function has(string|int $key): bool
	{
		return $this->storage->has($key);
	}

	public function set(string $key, mixed $value, int $retention): void
	{
		$this->last_modified_key = $key;
		$this->storage->set($key, $value);
	}

	public function increment(string $key, int $step = 1): void
	{
		$this->last_modified_key = $key;
		$this->storage->increment($key, $step);
	}

	public function decrement(string $key, int $step = 1): void
	{
		$this->last_modified_key = $key;
		$this->storage->increment($key, $step);
	}

	public function get(string $key): mixed
	{
		return $this->storage->get($key);
	}

	public function remove(string $key): void
	{
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

}