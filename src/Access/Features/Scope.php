<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 *
 * Inspired by Laravel rate limiting.
 */

namespace Rovota\Core\Access\Features;

use Rovota\Core\Access\Features\Interfaces\FeatureInterface;
use Rovota\Core\Auth\AuthManager;
use Rovota\Core\Structures\Bucket;

final class Scope
{

	protected mixed $data = null;

	protected Bucket $cache;

	// -----------------

	public function __construct(mixed $data = null)
	{
		$this->data = $data;
		$this->cache = new Bucket();
	}

	// -----------------

	public function get(string $name): FeatureInterface|null
	{
		return FeatureManager::get($name)?->withScope($this);
	}

	// -----------------

	public function active(string $name): bool
	{
		return $this->get($name)?->active() ?? false;
	}

	public function value(string $name, mixed $default = null): mixed
	{
		return $this->get($name)?->value($default) ?? $default;
	}

	// -----------------

	public function forget(string $name): void
	{
		$this->cache->remove($name);
	}

	public function update(string $name, mixed $value): void
	{
		$this->cache->set($name, $value);
	}

	// -----------------

	/**
	 * @internal
	 */
	public function getData(): mixed
	{
		if ($this->data === null) {
			$this->data = AuthManager::activeProvider()?->identity() ?? null;
		}
		return $this->data;
	}

	/**
	 * @internal
	 */
	public function getCache(): Bucket
	{
		return $this->cache;
	}

}