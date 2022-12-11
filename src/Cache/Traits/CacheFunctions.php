<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Traits;

use Rovota\Core\Kernel\Application;
use Rovota\Core\Structures\Map;

trait CacheFunctions
{

	public function all(): Map
	{
		return new Map($this->adapter->all());
	}

	// -----------------

	public function set(string|int|array $key, mixed $value = null, int|null $retention = null): void
	{
		if ($this->isRetentionDisabled()) {
			return;
		}
		foreach (is_array($key) ? $key : [$key => $value] as $key => $value) {
			$this->adapter->set($this->getFullKey($key), $value, $this->getRetentionPeriod($retention));
		}
	}

	public function forever(string|int|array $key, mixed $value = null): void
	{
		$this->set($key, $value, 31536000);
	}

	// -----------------

	public function has(string|int|array $key): bool
	{
		foreach (is_array($key) ? $key : [$key] as $key) {
			if ($this->adapter->has($this->getFullKey($key)) === false) {
				return false;
			}
		}

		return true;
	}

	public function missing(string|int|array $key): bool
	{
		foreach (is_array($key) ? $key : [$key] as $key) {
			if ($this->adapter->has($this->getFullKey($key)) === true) {
				return false;
			}
		}

		return true;
	}

	// -----------------

	public function pull(string|int|array $key, mixed $default = null): mixed
	{
		if (is_array($key)) {
			$items = $key;
			$result = [];
			foreach ($items as $key) {
				$result[$key] = $this->get($key) ?? ($default[$key] ?? null);
				$this->remove($key);
			}
		} else {
			$result = $this->adapter->get($key) ?? $default;
			$this->remove($key);
		}

		return $result;
	}

	public function get(string|int|array $key, mixed $default = null): mixed
	{
		if (is_array($key)) {
			$items = $key;
			$result = [];
			foreach ($items as $key) {
				$result[$key] = $this->get($key) ?? ($default[$key] ?? null);
			}
		} else {
			$result = $this->get($key) ?? $default;
		}

		return $result;
	}

	public function remember(string|int $key, callable $callback, int|null $retention = null): mixed
	{
		if ($this->has($key)) {
			return $this->get($key);
		}

		$value = $callback();
		$this->set($key, $value, $retention);
		return $value;
	}

	public function rememberForever(string|int $key, callable $callback): mixed
	{
		return $this->remember($key, $callback, 31536000);
	}

	// -----------------

	public function increment(string|int $key, int $step = 1): void
	{
		$this->adapter->increment($this->getFullKey($key), $step);
	}

	public function decrement(string|int $key, int $step = 1): void
	{
		$this->adapter->decrement($this->getFullKey($key), $step);
	}

	// -----------------

	public function remove(string|int|array $key): void
	{
		foreach (is_array($key) ? $key : [$key] as $key) {
			$this->adapter->remove($this->getFullKey($key));
		}
	}

	// -----------------

	public function flush(): void
	{
		$this->adapter->flush();
	}

	// -----------------

	protected function isRetentionDisabled(): bool
	{
		if (Application::isEnvironment($this->config->disabled_for)) {
			return true;
		}
		return $this->config->retention === 0;
	}

	protected function getRetentionPeriod(int|null $retention): int
	{
		return $retention ?? $this->config->retention;
	}

	protected function getFullKey(string|int $key): string
	{
		if ($this->prefix === null || strlen($this->prefix ?? '') === 0) {
			return (string) $key;
		}
		return $this->prefix.':'.$key;
	}

}