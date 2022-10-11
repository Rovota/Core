<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Cache;

use Redis;
use RedisException;
use Rovota\Core\Support\Arr;

class RedisStore extends CacheStore
{

	protected Redis $redis;

	// -----------------

	/**
	 * @throws RedisException
	 */
	public function __construct(string $name, array $options)
	{
		parent::__construct($name, $options);
		$this->setPrefix($options['prefix'] ?? $name);

		$redis = new Redis();
		$redis->connect($this->options['host'] ?? '127.0.0.1', $this->options['port']);
		$redis->auth($this->options['password']);
		$redis->select($this->options['database'] ?? 2);

		$this->redis = $redis;
	}

	// -----------------

	/**
	 * @throws RedisException
	 */
	public function put(string|int $key, mixed $value, int|null $retention = null): void
	{
		$retention = $this->getRetention($retention);
		if ($retention === 0) {
			return;
		}
		$this->actionPut($key, $retention);
		$this->redis->set($this->prefix.$key, $this->serialize($value), $retention);
	}

	/**
	 * @throws RedisException
	 */
	public function putMany(array $values, int|null $retention = null): void
	{
		foreach ($values as $key => $value) {
			$this->put($key, $value, $retention);
		}
	}

	/**
	 * @throws RedisException
	 */
	public function putAllExcept(array $values, string|array $except, int|null $retention = null): void
	{
		$except = is_string($except) ? [$except] : $except;
		foreach ($values as $key => $value) {
			if (!in_array($key, $except)) {
				$this->put($key, $value, $retention);
			}
		}
	}

	/**
	 * @throws RedisException
	 */
	public function forever(string|int $key, mixed $value): void
	{
		$this->put($key, $value, 31536000);
	}

	// -----------------

	/**
	 * @throws RedisException
	 */
	public function has(string|int $key): bool
	{
		return $this->redis->exists($this->prefix.$key) === 1;
	}

	/**
	 * @throws RedisException
	 */
	public function hasAll(array $keys): bool
	{
		foreach ($keys as $key) {
			if ($this->has($key) === false) {
				return false;
			}
		}
		return true;
	}

	/**
	 * @throws RedisException
	 */
	public function missing(string|int $key): bool
	{
		return $this->redis->exists($this->prefix.$key) === 0;
	}

	// -----------------

	/**
	 * @throws RedisException
	 */
	public function pull(string|int $key, mixed $default = null): mixed
	{
		if ($this->has($key)) {
			$result = $this->read($key);
			$this->forget($key);
		}
		return $result ?? $default;
	}

	/**
	 * @throws RedisException
	 */
	public function pullMany(array $keys, array $defaults = []): array
	{
		$result = [];
		foreach ($keys as $key) {
			$result[$key] = $this->read($key, $defaults[$key] ?? null);
		}
		$this->forgetMany($keys);
		return Arr::whereNotNull($result);
	}

	// -----------------

	/**
	 * @throws RedisException
	 */
	public function read(string|int $key, mixed $default = null): mixed
	{
		return $this->has($key) ? $this->deserialize($this->redis->get($this->prefix.$key)) : $default;
	}

	/**
	 * @throws RedisException
	 */
	public function readMany(array $keys, array $defaults = []): array
	{
		$entries = [];
		foreach ($keys as $key) {
			$entries[$key] = $this->read($key, $defaults[$key] ?? null);
		}
		return Arr::whereNotNull($entries);
	}

	// -----------------

	/**
	 * @throws RedisException
	 */
	public function remember(string|int $key, callable $callback, int|null $retention = null): mixed
	{
		if ($this->has($key)) {
			return $this->read($key);
		}

		$result = $callback();
		$this->put($key, $result, $retention);

		return $result;
	}

	/**
	 * @throws RedisException
	 */
	public function rememberForever(string|int $key, callable $callback): mixed
	{
		if ($this->has($key)) {
			return $this->read($key);
		}

		$result = $callback();
		$this->forever($key, $result);

		return $result;
	}

	// -----------------

	/**
	 * @throws RedisException
	 */
	public function increment(string|int $key, int $step = 1): void
	{
		$this->actionUpdate($key);
		$this->redis->incrBy($this->prefix.$key, max($step, 0));
	}

	/**
	 * @throws RedisException
	 */
	public function decrement(string|int $key, int $step = 1): void
	{
		$this->actionUpdate($key);
		$this->redis->decrBy($this->prefix.$key, max($step, 0));
	}

	// -----------------

	/**
	 * @throws RedisException
	 */
	public function forget(string|int $key): void
	{
		$this->actionForget($key);
		$this->redis->del($this->prefix.$key);
	}

	/**
	 * @throws RedisException
	 */
	public function forgetMany(array $keys): void
	{
		$items = [];
		foreach ($keys as $key) {
			$this->actionForget($key);
			$items[] = $this->prefix.$key;
		}
		if (empty($items) === false) {
			$this->redis->del($items);
		}
	}

	// -----------------

	/**
	 * @throws RedisException
	 */
	public function flush(): void
	{
		$this->redis->flushDB();
	}

}