<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache;

use Rovota\Core\Cache\Interfaces\CacheInterface;
use Rovota\Core\Kernel\Application;

abstract class CacheStore implements CacheInterface
{

	protected string $name;
	protected array $options;
	protected string $prefix;

	protected array $actions = [];
	protected string|null $last_modified_key = null;

	// -----------------

	public function __construct(string $name, array $options)
	{
		$this->name = $name;
		$this->options = $options;

		$this->setPrefix($name);
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

	// -----------------

	public function label(): string
	{
		return $this->options['label'];
	}

	public function driver(): string
	{
		return $this->options['driver'];
	}

	public function option(string $key): mixed
	{
		return $this->options[$key];
	}

	// -----------------

	public function getPrefix(): string
	{
		return $this->prefix;
	}

	public function setPrefix(string $prefix): void
	{
		$this->prefix = !empty($prefix) ? $prefix.':' : '';
	}

	// -----------------

	public function lastModifiedKey(): string|null
	{
		return $this->last_modified_key;
	}

	// -----------------

	public function actions(): array
	{
		return $this->actions;
	}

	// -----------------

	protected function actionPut(string|int $key, int|null $retention = null): void
	{
		$this->actions[] = [
			'type' => 'put',
			'key' => $key,
			'retention' => $retention,
		];
		$this->setLastModifiedKey($key);
	}

	protected function actionUpdate(string|int $key, int|null $retention = null): void
	{
		$this->actions[] = [
			'type' => 'update',
			'key' => $key,
			'retention' => $retention,
		];
		$this->setLastModifiedKey($key);
	}

	protected function actionForget(string|int $key): void
	{
		$this->actions[] = [
			'type' => 'forget',
			'key' => $key,
		];
		$this->setLastModifiedKey($key);
	}

	// -----------------

	protected function serialize($value): string
	{
		return is_numeric($value) && ! in_array($value, [INF, -INF]) && ! is_nan($value) ? $value : serialize($value);
	}

	protected function deserialize($value): mixed
	{
		return is_numeric($value) ? $value : unserialize($value);
	}

	// -----------------

	protected function getRetention(int|null $retention = null): int
	{
		if (Application::isEnvironment($this->options['disable_for'] ?? [])) {
			return 0;
		}
		return $retention ?? $this->options['retention'] ?? 0;
	}

	protected function setLastModifiedKey(string|int $key): void
	{
		$this->last_modified_key = $key;
	}


}