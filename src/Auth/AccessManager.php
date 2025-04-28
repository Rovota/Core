<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Auth;

use Rovota\Core\Http\RequestManager;

final class AccessManager
{

	protected static string $csrf_token;

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
		self::$permissions = new Bucket();
		self::$roles = new Bucket();

		self::initializeCsrfToken();
	}

	// -----------------
	// CSRF

	public static function getCsrfToken(): string
	{
		return self::$csrf_token;
	}

	public static function getCsrfTokenName(): string
	{
		return Registry::string('security_csrf_token', 'csrf_protection_token');
	}

	public static function verifyCsrfToken(string|null $token = null): bool
	{
		if ($token === null) {
			$token = RequestManager::getRequest()->post->get(self::getCsrfTokenName());
		}
		return self::$csrf_token === $token;
	}

	// -----------------
	// Internal

	protected static function initializeCsrfToken(): void
	{
		$token_name = self::getCsrfTokenName();

		$cookie = CookieManager::findReceived($token_name);
		if ($cookie instanceof Cookie) {
			self::$csrf_token = $cookie->value;
			return;
		}

		$token_value = Str::random(80);
		self::$csrf_token = $token_value;
	}

}