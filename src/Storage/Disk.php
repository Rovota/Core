<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\ReadOnly\ReadOnlyFilesystemAdapter;
use Rovota\Core\Http\RequestManager;
use Rovota\Core\Kernel\Application;
use Rovota\Core\Storage\Interfaces\DiskInterface;
use Rovota\Core\Storage\Traits\DiskFunctions;
use Rovota\Core\Support\Str;
use Rovota\Core\Support\Traits\Conditionable;

abstract class Disk implements DiskInterface
{
	use DiskFunctions, Conditionable;

	protected string $name;

	protected DiskConfig $config;

	protected FilesystemAdapter $adapter;

	protected Filesystem $flysystem;

	// -----------------

	public function __construct(string $name, FilesystemAdapter $adapter, DiskConfig $config)
	{
		$this->name = $name;
		$this->config = $config;

		$this->adapter = $this->config->read_only ? new ReadOnlyFilesystemAdapter($adapter) : $adapter;
		$this->flysystem = new Filesystem($this->adapter);
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
		return StorageManager::getDefault() === $this->name;
	}

	// -----------------

	public function name(): string
	{
		return $this->name;
	}

	public function config(): DiskConfig
	{
		return $this->config;
	}

	// -----------------

	public function domain(): string
	{
		$domain = $this->config->domain;
		$fallback = RequestManager::getRequest()->targetHost();

		if (is_array($domain)) {
			return $domain[Application::getEnvironment()] ?? $fallback;
		}

		return $domain ?? $fallback;
	}

	public function root(): string
	{
		return Str::trim($this->config->root, '/');
	}

	public function baseUrl(): string
	{
		$root = Str::startAndFinish($this->config->root, '/');
		$scheme = Application::$server->get('REQUEST_SCHEME', 'https');
		$base = sprintf('%s://%s%s', $scheme, $this->domain(), $root);

		return Str::trim($base, '/');
	}

	// -----------------

	public function flysystem(): Filesystem
	{
		return $this->flysystem;
	}

}