<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage;

use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Storage\Drivers\Custom;
use Rovota\Core\Storage\Drivers\Local;
use Rovota\Core\Storage\Drivers\Sftp;
use Rovota\Core\Storage\Drivers\S3;
use Rovota\Core\Storage\Enums\Driver;
use Rovota\Core\Storage\Exceptions\DiskMisconfigurationException;
use Rovota\Core\Storage\Exceptions\MissingDiskConfigException;
use Rovota\Core\Storage\Exceptions\UnsupportedDriverException;
use Rovota\Core\Storage\Interfaces\DiskInterface;
use Throwable;

final class StorageManager
{
	/**
	 * @var array<string, DiskInterface>
	 */
	protected static array $disks = [];

	protected static array $configs = [];

	protected static string|null $default = null;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 */
	public static function initialize(): void
	{
		$file = require base_path('config/disks.php');

		foreach ($file['disks'] as $name => $config) {
			self::define($name, $config);
		}

		self::setDefault($file['default']);
	}

	// -----------------

	public static function define(string $name, array $config): void
	{
		self::$configs[$name] = $config;

		if ($config['auto_connect']) {
			self::connect($name);
		}
	}

	public static function connect(string $name): void
	{
		if (isset(self::$configs[$name]) === false) {
			ExceptionHandler::addThrowable(new MissingDiskConfigException("There is no config found for a disk named '$name'."));
		}
		self::$disks[$name] = self::build($name, self::$configs[$name]);
	}

	public static function build(string $name, array $config): DiskInterface
	{
		$config = new DiskConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::addThrowable(new UnsupportedDriverException("The selected driver '{$config->get('driver')}' is not supported."));
		}

		if ($config->isValid() === false) {
			ExceptionHandler::addThrowable(new DiskMisconfigurationException("The disk '$name' cannot be used due to a configuration issue."));
		}

		return match ($config->driver) {
			Driver::Custom => new Custom($name, $config),
			Driver::Local => new Local($name, $config),
			Driver::S3 => new S3($name, $config),
			Driver::Sftp => new Sftp($name, $config),
		};
	}

	// -----------------

	public static function isDefined(string $name): bool
	{
		return array_key_exists($name, self::$configs);
	}

	public static function isConnected(string $name): bool
	{
		return array_key_exists($name, self::$disks);
	}

	// -----------------

	public static function get(string|null $name = null): DiskInterface|null
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
		if (isset(self::$configs[$name]) === false) {
			ExceptionHandler::addThrowable(new MissingDiskConfigException("Undefined disks cannot be set as default: '$name'."));
		}
		self::$default = $name;
	}

}