<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Security\Encrypter;

final class Crypt
{

	protected static array $encryption_config;

	protected static Encrypter|null $encrypter = null;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Security\Exceptions\IncorrectKeyException
	 */
	public static function initialize(): void
	{
		self::$encryption_config = require base_path('config/encryption.php');
		self::$encrypter = new Encrypter(base64_decode(self::$encryption_config['key'], self::$encryption_config['cipher']));
	}

	// -----------------

	public static function supports(string $key, string $cipher): bool
	{
		return Encrypter::supports($key, $cipher);
	}

	public static function generateKey(string $cipher): string
	{
		return Encrypter::generateKey($cipher);
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Security\Exceptions\EncryptionException
	 * @throws \Rovota\Core\Security\Exceptions\IncorrectKeyException
	 */
	public static function encrypt(mixed $value, bool $serialize = true): string
	{
		if (self::$encrypter === null) {
			self::initialize();
		}
		return self::$encrypter->encrypt($value, $serialize);
	}

	/**
	 * @throws \Rovota\Core\Security\Exceptions\EncryptionException
	 * @throws \Rovota\Core\Security\Exceptions\IncorrectKeyException
	 */
	public static function encryptString(string $string): string
	{
		if (self::$encrypter === null) {
			self::initialize();
		}
		return self::$encrypter->encryptString($string);
	}

	/**
	 * @throws \Rovota\Core\Security\Exceptions\PayloadException
	 * @throws \Rovota\Core\Security\Exceptions\IncorrectKeyException
	 */
	public static function decrypt(string $payload, bool $serialize = true): mixed
	{
		if (self::$encrypter === null) {
			self::initialize();
		}
		return self::$encrypter->decrypt($payload, $serialize);
	}

	/**
	 * @throws \Rovota\Core\Security\Exceptions\PayloadException
	 * @throws \Rovota\Core\Security\Exceptions\IncorrectKeyException
	 */
	public static function decryptString(string $string): string
	{
		if (self::$encrypter === null) {
			self::initialize();
		}
		return self::$encrypter->decryptString($string);
	}

}