<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Adapters;

use Redis;
use RedisException;
use Rovota\Core\Cache\Interfaces\CacheAdapter;
use Rovota\Core\Kernel\Resolver;
use Rovota\Core\Support\Config;

class RedisAdapter implements CacheAdapter
{

	protected Redis $redis;

	protected string|null $last_modified_key = null;

	// -----------------

	/**
	 * @throws RedisException
	 */
	public function __construct(Config $parameters)
	{
		$this->redis = new Redis();
		$this->redis->connect($parameters->string('host', '127.0.0.1'));
		$this->redis->auth($parameters->get('password'));
		$this->redis->select($parameters->int('database', 2));
	}

	// -----------------

	public function all(): array
	{
		return [];
	}

	/**
	 * @throws RedisException
	 */
	public function has(string $key): bool
	{
		$result = $this->redis->exists($key);
		return $result === 1 || $result === true;
	}

	/**
	 * @throws RedisException
	 */
	public function set(string $key, mixed $value, int $retention): void
	{
		$this->last_modified_key = $key;
		$this->redis->set($key, Resolver::serialize($value), $retention);
	}

	/**
	 * @throws RedisException
	 */
	public function increment(string $key, int $step = 1): void
	{
		$this->last_modified_key = $key;
		$this->redis->incrBy($key, max($step, 0));
	}

	/**
	 * @throws RedisException
	 */
	public function decrement(string $key, int $step = 1): void
	{
		$this->last_modified_key = $key;
		$this->redis->decrBy($key, max($step, 0));
	}

	/**
	 * @throws RedisException
	 */
	public function get(string $key): mixed
	{
		return $this->redis->exists($key) ? Resolver::deserialize($this->redis->get($key)) : null;
	}

	/**
	 * @throws RedisException
	 */
	public function remove(string $key): void
	{
		$this->last_modified_key = $key;
		$this->redis->del($key);
	}

	// -----------------

	/**
	 * @throws RedisException
	 */
	public function flush(): void
	{
		$this->redis->flushDB();
	}

	// -----------------

	public function lastModifiedKey(): string|null
	{
		return $this->last_modified_key;
	}

}