<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Logging;

use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Logging\Drivers\Discord;
use Rovota\Core\Logging\Drivers\Monolog;
use Rovota\Core\Logging\Drivers\Stack;
use Rovota\Core\Logging\Drivers\Stream;
use Rovota\Core\Logging\Exceptions\MissingChannelConfigException;
use Rovota\Core\Logging\Exceptions\UnsupportedDriverException;
use Rovota\Core\Logging\Interfaces\LogInterface;
use Throwable;

final class LoggingManager
{
	/**
	 * @var array<string, LogInterface>
	 */
	protected static array $channels = [];

	protected static string|null $default = null;

	protected static array $config = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 * @throws \Rovota\Core\Logging\Exceptions\UnsupportedDriverException
	 * @throws \Rovota\Core\Logging\Exceptions\MissingChannelConfigException
	 */
	public static function initialize(): void
	{
		$config = require base_path('config/logging.php');
		self::$default = $config['default'];

		foreach ($config['channels'] as $name => $options) {
			self::$config[$name] = $options;
			self::connect($name);
		}
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Logging\Exceptions\UnsupportedDriverException
	 * @throws \Rovota\Core\Logging\Exceptions\MissingChannelConfigException
	 */
	public static function define(string $name, array $options, bool $connect = false): void
	{
		self::$config[$name] = $options;
		if ($connect) {
			self::connect($name);
		}
	}

	/**
	 * @throws \Rovota\Core\Logging\Exceptions\UnsupportedDriverException
	 * @throws \Rovota\Core\Logging\Exceptions\MissingChannelConfigException
	 */
	public static function connect(string $name): void
	{
		if (!isset(self::$config[$name])) {
			throw new MissingChannelConfigException("There is no config found for a channel named '$name'.");
		}
		self::$channels[$name] = self::build($name, self::$config[$name]);
	}

	/**
	 * @throws \Rovota\Core\Logging\Exceptions\UnsupportedDriverException
	 */
	public static function build(string $name, array $options): LogInterface
	{
		return match ($options['driver']) {
			'stack' => new Stack($name, $options),
			'stream' => new Stream($name, $options),
			'discord' => new Discord($name, $options),
			'monolog' => new Monolog($name, $options),
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
		return array_key_exists($name, self::$channels);
	}

	// -----------------

	public static function get(string|null $name = null): LogInterface
	{
		if ($name === null) {
			$name = self::$default;
		}
		if (!isset(self::$channels[$name])) {
			try {
				self::connect($name);
			} catch (Throwable $throwable) {
				ExceptionHandler::addThrowable($throwable, true);
				exit;
			}
		}
		return self::$channels[$name];
	}

	public static function options(string $name): array
	{
		return self::$config[$name] ?? [];
	}

	/**
	 * @returns array<string, LogInterface>
	 */
	public static function all(): array
	{
		return self::$channels;
	}

	// -----------------

	public static function getDefault(): string|null
	{
		return self::$default;
	}

	public static function setDefault(string $name): void
	{
		if (isset(self::$config[$name]) === false) {
			ExceptionHandler::logMessage('warning', "Undefined channels cannot be set as default: '{name}'.", ['name' => $name]);
		}
		self::$default = $name;
	}

}