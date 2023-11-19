<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache;

use Rovota\Core\Cache\Interfaces\CacheInterface;
use Rovota\Core\Cache\Interfaces\CacheAdapter;
use Rovota\Core\Cache\Traits\CacheFunctions;
use Rovota\Core\Support\Traits\Conditionable;

abstract class CacheStore implements CacheInterface
{
	use CacheFunctions, Conditionable;

	protected string $name;

	protected CacheConfig $config;

	protected CacheAdapter $adapter;

	// -----------------

	public function __construct(string $name, CacheAdapter $adapter, CacheConfig $config)
	{
		$this->name = $name;
		$this->config = $config;

		$this->adapter = $adapter;
	}

	// -----------------

	public function __toString(): string
	{
		return $this->name;
	}

	public function __get(string $name): mixed
	{
		return $this->config->get($name);
	}

	public function __isset(string $name): bool
	{
		return $this->config->has($name);
	}

	// -----------------

	public function isDefault(): bool
	{
		return CacheManager::getDefault() === $this->name;
	}

	// -----------------

	public function name(): string
	{
		return $this->name;
	}

	public function config(): CacheConfig
	{
		return $this->config;
	}

	// -----------------

	public function adapter(): CacheAdapter
	{
		return $this->adapter;
	}

	// -----------------

	 public function lastModifiedKey(): string|null
	 {
	 	return $this->adapter->lastModifiedKey();
	 }

}