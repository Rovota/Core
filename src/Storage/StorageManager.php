<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Storage;

use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Storage\Exceptions\MissingStorageConfigException;
use Rovota\Core\Storage\Exceptions\UnsupportedDriverException;
use Rovota\Core\Storage\Interfaces\DiskInterface;
use Throwable;

final class StorageManager
{
	/**
	 * @var array<string, DiskInterface>
	 */
	protected static array $disks = [];

	protected static string|null $default = null;

	protected static array $config = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 * @throws \Rovota\Core\Storage\Exceptions\UnsupportedDriverException
	 * @throws \Rovota\Core\Storage\Exceptions\MissingStorageConfigException
	 */
	public static function initialize(): void
	{
		$config = require base_path('config/disks.php');
		self::$default = $config['default'];

		foreach ($config['disks'] as $name => $options) {
			self::$config[$name] = $options;
			if ($options['auto_connect'] === false) {
				continue;
			}
			self::connect($name);
		}
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Storage\Exceptions\UnsupportedDriverException
	 * @throws \Rovota\Core\Storage\Exceptions\MissingStorageConfigException
	 */
	public static function define(string $name, array $options, bool $connect = false): void
	{
		self::$config[$name] = $options;
		if ($connect) {
			self::connect($name);
		}
	}

	/**
	 * @throws \Rovota\Core\Storage\Exceptions\UnsupportedDriverException
	 * @throws \Rovota\Core\Storage\Exceptions\MissingStorageConfigException
	 */
	public static function connect(string $name): void
	{
		if (!isset(self::$config[$name])) {
			throw new MissingStorageConfigException("There is no config found for a disk named '$name'.");
		}
		self::$disks[$name] = self::build($name, self::$config[$name]);
	}

	/**
	 * @throws \Rovota\Core\Storage\Exceptions\UnsupportedDriverException
	 */
	public static function build(string $name, array $options): DiskInterface
	{
		return match ($options['driver']) {
			'local' => new LocalDisk($name, $options),
			's3' => new CloudDisk($name, $options),
			'sftp' => new RemoteDisk($name, $options),
			'flysystem' => new FlysystemDisk($name, $options),
			default => throw new UnsupportedDriverException("The selected driver '{$options['driver']}' is not supported.")
		};
	}

	// -----------------

	public static function isDefined(string $name): bool
	{
		return array_key_exists($name, self::$config);
	}

	public static function isActive(string $name): bool
	{
		return array_key_exists($name, self::$disks);
	}

	// -----------------

	public static function get(string|null $name = null): DiskInterface
	{
		if ($name === null) {
			$name = self::$default;
		}
		if (!isset(self::$disks[$name])) {
			try {
				self::connect($name);
			} catch (Throwable $throwable) {
				ExceptionHandler::addThrowable($throwable, true);
				exit;
			}
		}
		return self::$disks[$name];
	}

	public static function options(string $name): array
	{
		return self::$config[$name] ?? [];
	}

	/**
	 * @returns array<string, DiskInterface>
	 */
	public static function all(): array
	{
		return self::$disks;
	}

	// -----------------

	public static function getDefault(): string|null
	{
		return self::$default;
	}

	public static function setDefault(string $name): void
	{
		if (isset(self::$config[$name]) === false) {
			ExceptionHandler::logMessage('warning', "Undefined disks cannot be set as default: '{name}'.", ['name' => $name]);
		}
		self::$default = $name;
	}

}