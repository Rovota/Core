<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Cookie;

use DateTime;
use Rovota\Core\Facades\Crypt;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Support\Moment;
use Throwable;

final class Cookie
{

	public readonly string $name;

	public string $value;

	protected array $options = [
		'domain' => null,
		'expires' => 0,
		'path' => '/',
		'httponly' => true,
		'secure' => true,
		'samesite' => 'Lax'
	];

	protected bool $received = false;

	// -----------------

	public function __construct(string $name, string $value, array $options = [], bool $received = false)
	{
		$this->options['domain'] = cookie_domain();

		$this->received = $received;
		$this->name = $this->stripPrefix($name);
		$this->value = $value;

		$this->setOptions($options);
	}

	public function __toString(): string
	{
		return $this->name;
	}

	// -----------------

	public function prefixedName(): string
	{
		return $this->addPrefix($this->name);
	}

	public function contains(string $value): bool
	{
		return str_contains($this->value, $value);
	}

	// -----------------

	public function update(string|null $value, array $options = []): void
	{
		if ($value !== null) {
			$this->value = $value;
		}
		$this->setOptions($options);
	}

	public function apply(): bool
	{
		try {
			$value = CookieManager::hasEncryptionEnabled($this->name) ? Crypt::encryptString($this->value) : $this->value;
			return setcookie($this->prefixedName(), $value, $this->options);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
			return false;
		}
	}

	public function expire(): bool
	{
		$this->options['expires'] = -1;
		return setcookie($this->prefixedName(), '', $this->options);
	}

	// -----------------

	protected function addPrefix(string $name): string
	{
		return sprintf('__Secure-%s', $name);
	}

	protected function stripPrefix(string $name): string
	{
		return str_replace('__Secure-', '', trim($name));
	}

	protected function setOptions(array $options): void
	{
		foreach ($options as $key => $value) {

			if ($key === 'expires') {
				$value = match(true) {
					$value instanceof Moment => (int)$value->toEpochString(),
					$value instanceof DateTime => (int)$value->format('U'),
					default => time() + ($value * 60),
				};
			}

			if (isset($this->options[$key])) {
				$this->options[$key] = $value;
			}
		}
	}

}