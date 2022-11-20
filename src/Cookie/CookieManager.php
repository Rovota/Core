<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cookie;

use Rovota\Core\Facades\Crypt;
use Rovota\Core\Facades\Registry;
use Rovota\Core\Support\Helpers\Arr;
use Throwable;

final class CookieManager
{
	/**
	 * @var array<string, Cookie>
	 */
	protected static array $received = [];

	/**
	 * @var array<string, Cookie>
	 */
	protected static array $queued = [];

	protected static array $except = [];

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
		self::$except = array_merge(['locale', 'csrf_protection_token'], Registry::array('core_plaintext_cookies', []));

		foreach ($_COOKIE as $name => $value) {
			if ($name === '__Secure-'.Registry::string('core_session_cookie_name', 'session')) {
				continue;
			}

			if (self::hasEncryptionEnabled(str_replace('__Secure-', '', trim($name)))) {
				try {
					$value = Crypt::decryptString($value);
				} catch (Throwable) {
					continue;
				}
			}

			$cookie = new Cookie($name, $value, received: true);
			self::$received[$cookie->name] = $cookie;
		}
	}

	// -----------------

	public static function make(string $name, string|null $value, array $options = []): Cookie
	{
		return new Cookie($name, $value, $options);
	}

	public static function forever(string $name, string|null $value, array $options = []): Cookie
	{
		$options = array_merge($options, ['expires' => now()->addDays(400)]);
		return new Cookie($name, $value, $options);
	}

	// -----------------

	public static function queue(Cookie|string $name, string|null $value = null, array $options = []): void
	{
		if ($name instanceof Cookie) {
			$cookie = $name;
		} else {
			$cookie = new Cookie($name, $value, $options);
		}
		self::$queued[$cookie->name] = $cookie;
	}

	public static function applyQueue(): void
	{
		foreach (self::$queued as $name => $cookie) {
			$cookie->apply();
			unset(self::$queued[$name]);
		}
	}

	public static function isQueued(string $name): bool
	{
		return isset(self::$queued[$name]);
	}

	public static function findQueued(string $name): Cookie|null
	{
		return self::$queued[$name] ?? null;
	}

	public static function removeQueued(string $name): void
	{
		unset(self::$queued[$name]);
	}

	/**
	 * @returns array<string, Cookie>
	 */
	public static function getQueued(): array
	{
		return self::$queued;
	}

	// -----------------

	public static function recycle(string $name, string|null $value = null, array $options = []): void
	{
		$cookie = self::findReceived($name);
		if ($cookie instanceof Cookie) {
			$cookie->update($value ?? $cookie->value, $options);
			self::queue($cookie);
		}
	}

	public static function expire(string $name): void
	{
		$cookie = self::findReceived($name);
		if ($cookie instanceof Cookie) {
			$cookie->update('', ['expires' => -1]);
			self::queue($cookie);
		}
	}

	public static function isReceived(string $name): bool
	{
		return isset(self::$received[$name]);
	}

	public static function findReceived(string $name): Cookie|null
	{
		return self::$received[$name] ?? null;
	}

	/**
	 * @returns array<string, Cookie>
	 */
	public static function getReceived(): array
	{
		return self::$received;
	}

	// -----------------
	
	public static function hasEncryptionEnabled(string $name): bool
	{
		return Arr::contains(self::$except, $name) === false;
	}

}