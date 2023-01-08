<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Logging;

use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Logging\Drivers\Discord;
use Rovota\Core\Logging\Drivers\Monolog;
use Rovota\Core\Logging\Drivers\Stack;
use Rovota\Core\Logging\Drivers\Stream;
use Rovota\Core\Logging\Enums\Driver;
use Rovota\Core\Logging\Exceptions\ChannelMisconfigurationException;
use Rovota\Core\Logging\Exceptions\MissingChannelConfigException;
use Rovota\Core\Logging\Exceptions\UnsupportedDriverException;
use Rovota\Core\Logging\Interfaces\ChannelInterface;
use Throwable;

final class LoggingManager
{
	/**
	 * @var array<string, ChannelInterface>
	 */
	protected static array $channels = [];

	protected static array $configs = [];

	protected static string|null $default = null;

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
		$file = require base_path('config/logging.php');

		foreach ($file['channels'] as $name => $config) {
			self::define($name, $config);
		}

		self::setDefault($file['default']);
	}

	// -----------------
	
	public static function define(string $name, array $config): void
	{
		self::$configs[$name] = $config;

		// if ($config['auto_connect']) {
			self::connect($name);
		// }
	}
	
	public static function connect(string $name): void
	{
		if (isset(self::$configs[$name]) === false) {
			ExceptionHandler::addThrowable(new MissingChannelConfigException("There is no config found for a channel named '$name'."));
		}
		self::$channels[$name] = self::build($name, self::$configs[$name]);
	}

	public static function build(string $name, array $config): ChannelInterface|null
	{
		$config = new ChannelConfig($config);

		if (Driver::isSupported($config->get('driver')) === false) {
			ExceptionHandler::addThrowable(new UnsupportedDriverException("The selected driver '{$config->get('driver')}' is not supported."));
			return null;
		}

		if ($config->isValid() === false) {
			ExceptionHandler::addThrowable(new ChannelMisconfigurationException("The channel '$name' cannot be used due to a configuration issue."));
			return null;
		}

		return match($config->driver) {
			Driver::Discord => new Discord($name, $config),
			Driver::Monolog => new Monolog($name, $config),
			Driver::Stack => new StackChannel($name, $config),
			Driver::Stream => new Stream($name, $config),
			default => null,
		};
	}

	// -----------------

	public static function isDefined(string $name): bool
	{
		return array_key_exists($name, self::$configs);
	}

	public static function isConnected(string $name): bool
	{
		return array_key_exists($name, self::$channels);
	}

	// -----------------

	public static function get(string|null $name = null): ChannelInterface
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

	/**
	 * @returns array<string, ChannelInterface>
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
		if (isset(self::$configs[$name]) === false) {
			ExceptionHandler::addThrowable(new MissingChannelConfigException("Undefined channels cannot be set as default: '$name'."));
		}
		self::$default = $name;
	}

}