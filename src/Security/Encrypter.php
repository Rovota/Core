<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 *
 * Inspired by Laravel Illuminate/Encryption/Encrypter class.
 */

namespace Rovota\Core\Security;

use Exception;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Security\Exceptions\EncryptionException;
use Rovota\Core\Security\Exceptions\IncorrectKeyException;
use Rovota\Core\Security\Exceptions\PayloadException;
use Throwable;

/**
 * @internal
 */
final class Encrypter
{

	protected string $key;

	protected string $cipher;

	protected static array $supported_ciphers = ['aes-128-cbc' => ['size' => 16, 'aead' => false], 'aes-256-cbc' => ['size' => 32, 'aead' => false], 'aes-128-gcm' => ['size' => 16, 'aead' => true], 'aes-256-gcm' => ['size' => 32, 'aead' => true],];

	// -----------------

	/**
	 * @throws IncorrectKeyException
	 */
	public function __construct(string $key, string|null $cipher = 'aes-256-gcm')
	{
		if (!Encrypter::supports($key, $cipher)) {
			$ciphers = as_bucket(self::$supported_ciphers)->keys()->join(', ', ' and ');
			throw new IncorrectKeyException("Unsupported cipher or incorrect key length. Supported ciphers are: $ciphers.");
		}

		$this->key = $key;
		$this->cipher = $cipher;
	}

	// -----------------

	public static function supports(string $key, string $cipher): bool
	{
		$cipher = strtolower($cipher);
		if (!isset(self::$supported_ciphers[$cipher])) {
			return false;
		}
		return mb_strlen($key, '8bit') === self::$supported_ciphers[$cipher]['size'];
	}

	public static function generateKey(string $cipher): string
	{
		$iteration = 0; $bytes = '';

		while ($iteration < 1) {
			try {
				$bytes = random_bytes(self::$supported_ciphers[strtolower($cipher)]['size'] ?? 32);
			} catch (Throwable $throwable) {
				ExceptionHandler::logThrowable($throwable);
			}
			$iteration++;
		}

		return $bytes;
	}

	public function getKey(): string
	{
		return $this->key;
	}

	public function getKeyEncoded(): string
	{
		return base64_encode($this->key);
	}

	public function getCipher(): string
	{
		return $this->cipher;
	}

	// -----------------

	/**
	 * @throws EncryptionException
	 * @throws Exception
	 */
	public function encrypt(mixed $value, bool $serialize = true): string
	{
		$cipher = strtolower($this->cipher);
		$iv = random_bytes(openssl_cipher_iv_length($cipher));
		$tag = '';

		$value = self::$supported_ciphers[$cipher]['aead'] ? openssl_encrypt($serialize ? serialize($value) : $value, $cipher, $this->key, 0, $iv, $tag) : openssl_encrypt($serialize ? serialize($value) : $value, $cipher, $this->key, 0, $iv);

		if ($value === false) {
			throw new EncryptionException('The given data could not be encrypted.');
		}

		$iv = base64_encode($iv);
		$tag = base64_encode($tag);

		$mac = self::$supported_ciphers[$cipher]['aead'] ? '' : $this->hash($iv, $value);
		$json = json_encode(compact('iv', 'value', 'mac', 'tag'), JSON_UNESCAPED_SLASHES);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new EncryptionException('The given data could not be encrypted.');
		}

		return base64_encode($json);
	}

	/**
	 * @throws EncryptionException
	 */
	public function encryptString(string $string): string
	{
		return $this->encrypt($string, false);
	}

	/**
	 * @throws PayloadException
	 */
	public function decrypt(string $payload, bool $deserialize = true): mixed
	{
		$cipher = strtolower($this->cipher);
		$payload = $this->getJsonPayload($payload);
		$iv = base64_decode($payload['iv']);
		$tag = empty($payload['tag']) ? null : base64_decode($payload['tag']);

		if (self::$supported_ciphers[$cipher]['aead'] && mb_strlen($tag) === 32) {
			throw new PayloadException('The given payload could not be decrypted.');
		}

		$decrypted = openssl_decrypt($payload['value'], $cipher, $this->key, 0, $iv, $tag ?? '');

		if ($decrypted === false) {
			throw new PayloadException('The given payload could not be decrypted.');
		}

		return $deserialize ? unserialize($decrypted) : $decrypted;
	}

	/**
	 * @throws PayloadException
	 */
	public function decryptString(string $payload): string
	{
		return $this->decrypt($payload, false);
	}

	// -----------------

	protected function hash(string $iv, mixed $value): string
	{
		return hash_hmac('sha256', $iv.$value, $this->key);
	}

	/**
	 * @throws PayloadException
	 */
	protected function getJsonPayload(string $payload): array
	{
		$payload = json_decode(base64_decode($payload), true);

		if (!$this->isValidPayload($payload)) {
			throw new PayloadException('The given payload is invalid.');
		}

		if (!self::$supported_ciphers[strtolower($this->cipher)]['aead'] && !$this->isValidMac($payload)) {
			throw new PayloadException('An invalid MAC has been provided.');
		}

		return $payload;
	}

	protected function isValidPayload(mixed $payload): bool
	{
		return is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac']) && strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length(strtolower($this->cipher));
	}

	protected function isValidMac(array $payload): bool
	{
		return hash_equals($this->hash($payload['iv'], $payload['value']), $payload['mac']);
	}

}