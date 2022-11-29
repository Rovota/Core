<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Session;

use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Session\Exceptions\MissingSessionConfigException;
use Rovota\Core\Session\Exceptions\UnsupportedDriverException;
use Rovota\Core\Session\Interfaces\SessionInterface;
use Throwable;

final class SessionManager
{
	/**
	 * @var array<string, SessionInterface>
	 */
	private static array $handlers = [];
	private static string|null $default = null;

	private static array $config = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 * @throws \Rovota\Core\Session\Exceptions\UnsupportedDriverException
	 * @throws \Rovota\Core\Session\Exceptions\MissingSessionConfigException
	 */
	public static function initialize(): void
	{
		$config = require base_path('config/sessions.php');
		self::$default = $config['default'];

		foreach ($config['handlers'] as $name => $options) {
			self::$config[$name] = $options;
			if ($options['auto_connect'] === false) {
				continue;
			}
			self::connect($name);
		}
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Session\Exceptions\UnsupportedDriverException
	 * @throws \Rovota\Core\Session\Exceptions\MissingSessionConfigException
	 */
	public static function define(string $name, array $options, bool $connect = false): void
	{
		self::$config[$name] = $options;
		if ($connect) {
			self::connect($name);
		}
	}

	/**
	 * @throws \Rovota\Core\Session\Exceptions\UnsupportedDriverException
	 * @throws \Rovota\Core\Session\Exceptions\MissingSessionConfigException
	 */
	public static function connect(string $name): void
	{
		if (!isset(self::$config[$name])) {
			throw new MissingSessionConfigException("There is no config found for a session named '$name'.");
		}
		$options = self::$config[$name];

		match ($options['driver']) {
			'cookie' => self::connectCookie($name, $options),
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
		return array_key_exists($name, self::$handlers);
	}

	// -----------------

	public static function get(string|null $name = null): SessionInterface
	{
		if ($name === null) {
			$name = self::$default;
		}
		if (!isset(self::$handlers[$name])) {
			try {
				self::connect($name);
			} catch (Throwable $throwable) {
				ExceptionHandler::addThrowable($throwable, true);
				exit;
			}
		}
		return self::$handlers[$name];
	}

	public static function options(string $name): array
	{
		return self::$config[$name] ?? [];
	}

	/**
	 * @returns array<string, SessionInterface>
	 */
	public static function all(): array
	{
		return self::$handlers;
	}

	// -----------------

	public static function getDefault(): string|null
	{
		return self::$default;
	}

	public static function setDefault(string $name): void
	{
		if (isset(self::$config[$name]) === false) {
			ExceptionHandler::logMessage('warning', "Undefined sessions cannot be set as default: '{name}'.", ['name' => $name]);
		}
		self::$default = $name;
	}

	// -----------------

	protected static function connectCookie(string $name, array $options): bool
	{
		self::$handlers[$name] = new CookieStore($name, $options);
		return true;
	}

}