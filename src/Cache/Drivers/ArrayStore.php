<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Drivers;

use Rovota\Core\Cache\CacheStore;
use Rovota\Core\Helpers\Arr;

class ArrayStore extends CacheStore
{

	protected array $storage = [];

	// -----------------

	public function put(string|int $key, mixed $value, int|null $retention = null): void
	{
		$retention = $this->getRetention($retention);
		if ($retention === 0) {
			return;
		}
		$this->actionPut($key, $retention);
		$this->storage[$this->prefix.$key] = $value;
	}

	public function putMany(array $values, int|null $retention = null): void
	{
		foreach ($values as $key => $value) {
			$this->put($key, $value, $retention);
		}
	}

	public function putAllExcept(array $values, string|array $except, int|null $retention = null): void
	{
		$except = is_string($except) ? [$except] : $except;
		foreach ($values as $key => $value) {
			if (!in_array($key, $except)) {
				$this->put($key, $value, $retention);
			}
		}
	}

	public function forever(string|int $key, mixed $value): void
	{
		$this->put($key, $value, 31536000);
	}

	// -----------------

	public function has(string|int|array $key): bool
	{
		$keys = is_array($key) ? $key : [$key];
		foreach ($keys as $key) {
			if (isset($this->storage[$this->prefix.$key]) === false) {
				return false;
			}
		}
		return true;
	}

	public function missing(string|int|array $key): bool
	{
		$keys = is_array($key) ? $key : [$key];
		foreach ($keys as $key) {
			if (isset($this->storage[$this->prefix.$key]) === true) {
				return false;
			}
		}
		return true;
	}

	// -----------------

	public function pull(string|int $key, mixed $default = null): mixed
	{
		if ($this->has($key)) {
			$result = $this->read($key);
			$this->forget($key);
		}
		return $result ?? $default;
	}

	public function pullMany(array $keys, array $defaults = []): array
	{
		$result = [];
		foreach ($keys as $key) {
			$result[$key] = $this->read($key, $defaults[$key] ?? null);
		}
		$this->forgetMany($keys);
		return Arr::filter($result, function ($value) {
			return $value !== null;
		});
	}

	// -----------------

	public function read(string|int $key, mixed $default = null): mixed
	{
		return $this->storage[$this->prefix.$key] ?? $default;
	}

	public function readMany(array $keys, array $defaults = []): array
	{
		$entries = [];
		foreach ($keys as $key) {
			$entries[$key] = $this->read($key, $defaults[$key] ?? null);
		}
		return Arr::filter($entries, function ($value) {
			return $value !== null;
		});
	}

	// -----------------

	public function remember(string|int $key, callable $callback, int|null $retention = null): mixed
	{
		if ($this->has($key)) {
			return $this->read($key);
		}

		$result = $callback();
		$this->put($key, $result, $retention);

		return $result;
	}

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

	public function increment(string|int $key, int $step = 1): void
	{
		$this->actionUpdate($key);
		$this->storage[$this->prefix.$key] = $this->storage[$this->prefix.$key] + max($step, 0);
	}

	public function decrement(string|int $key, int $step = 1): void
	{
		$this->actionUpdate($key);
		$this->storage[$this->prefix.$key] = $this->storage[$this->prefix.$key] - max($step, 0);
	}

	// -----------------

	public function forget(string|int $key): void
	{
		$this->actionForget($key);
		unset($this->storage[$this->prefix.$key]);
	}

	public function forgetMany(array $keys): void
	{
		foreach ($keys as $key) {
			$this->actionForget($key);
			unset($this->storage[$this->prefix.$key]);
		}
	}

	// -----------------

	public function flush(): void
	{
		$this->storage = [];
	}

}