<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Session;

use Rovota\Core\Facades\Registry;
use Rovota\Core\Session\Interfaces\SessionInterface;
use Rovota\Core\Support\ArrOld;

class CookieStore implements SessionInterface
{

	protected string $name;
	protected array $options;

	protected string $cookie_name;

	protected bool $loaded = false;

	// -----------------

	public function __construct(string $name, array $options)
	{
		$this->name = $name;
		$this->options = $options;

		$this->cookie_name = Registry::string('core_session_cookie_name', 'session');

		if (empty($_COOKIE[$this->cookie_name]) === false) {
			$this->loadSession();
		}
	}

	// -----------------

	public function name(): string
	{
		return $this->name;
	}

	public function label(): string
	{
		return $this->options['label'];
	}

	// -----------------

	public function all(): array
	{
		$this->loadSession();
		return $_SESSION['data'];
	}

	public function put(string|int $key, mixed $value): void
	{
		$this->loadSession();
		$_SESSION['data'][$key] = $value;
	}

	public function putMany(array $values): void
	{
		$this->loadSession();
		foreach ($values as $key => $value) {
			$_SESSION['data'][$key] = $value;
		}
	}

	public function putAllExcept(array $values, string|array $except): void
	{
		$this->loadSession();
		$except = is_string($except) ? [$except] : $except;
		foreach ($values as $key => $value) {
			if (in_array($key, $except) === false) {
				$_SESSION['data'][$key] = $value;
			}
		}
	}

	public function has(string|int $key): bool
	{
		$this->loadSession();
		return isset($_SESSION['data'][$key]);
	}

	public function hasAll(array $keys): bool
	{
		$this->loadSession();
		foreach ($keys as $key) {
			if (isset($_SESSION['data'][$key]) === false) {
				return false;
			}
		}
		return true;
	}

	public function missing(string|int $key): bool
	{
		$this->loadSession();
		return isset($_SESSION['data'][$key]) === false;
	}

	public function pull(string|int $key, mixed $default = null): mixed
	{
		$this->loadSession();
		$result = $_SESSION['data'][$key] ?? $default;
		unset($_SESSION['data'][$key]);
		return $result;
	}

	public function pullMany(array $keys, array $defaults = []): array
	{
		$this->loadSession();
		$result = [];
		foreach ($keys as $key) {
			$result[$key] = $_SESSION['data'][$key] ?? ($defaults[$key] ?? null);
			unset($_SESSION['data'][$key]);
		}
		return ArrOld::whereNotNull($result);
	}

	public function read(string|int $key, mixed $default = null): mixed
	{
		$this->loadSession();
		return $_SESSION['data'][$key] ?? $default;
	}

	public function readMany(array $keys, array $defaults = []): array
	{
		$this->loadSession();
		$entries = [];
		foreach ($keys as $key) {
			$entries[$key] = $_SESSION['data'][$key] ?? ($defaults[$key] ?? null);
		}
		return ArrOld::whereNotNull($entries);
	}

	public function remember(string|int $key, callable $callback): mixed
	{
		if (isset($_SESSION['data'][$key])) {
			return $_SESSION['data'][$key];
		}
		$result = $callback();
		$_SESSION['data'][$key] = $result;
		return $result;
	}

	public function increment(string|int $key, int $step = 1): void
	{
		$this->loadSession();
		$_SESSION['data'][$key] = $_SESSION['data'][$key] ?? max($step, 0);
	}

	public function decrement(string|int $key, int $step = 1): void
	{
		$this->loadSession();
		$_SESSION['data'][$key] = $_SESSION['data'][$key] ?? 0 - max($step, 0);
	}

	public function forget(string|int $key): void
	{
		$this->loadSession();
		unset($_SESSION['data'][$key]);
	}

	public function forgetMany(array $keys): void
	{
		$this->loadSession();
		foreach ($keys as $key) {
			unset($_SESSION['data'][$key]);
		}
	}

	public function flush(): void
	{
		$this->loadSession();
		$_SESSION['data'] = [];
	}

	// -----------------

	public function flash(string|int $key, mixed $value): void
	{
		$this->loadSession();
		$this->put($key, $value);
		$_SESSION['flashes'][] = $key;
		$this->removeFlashes([$key]);
	}

	public function flashMany(array $values): void
	{
		$this->loadSession();
		foreach ($values as $key => $value) {
			$this->flash($key, $value);
		}
	}

	public function reflash(): void
	{
		$this->loadSession();
		$this->setFlashes($_SESSION['flashes_old']);
		$_SESSION['flashes_old'] = [];
	}

	public function keep(array|string $keys): void
	{
		$this->loadSession();
		$keys = is_string($keys) ? [$keys] : $keys;
		$this->setFlashes($keys);
		$this->removeFlashes($keys);
	}

	// -----------------

	protected function setFlashes(array $keys)
	{
		$_SESSION['flashes'] = array_unique(array_merge($_SESSION['flashes'], $keys));
	}

	protected function removeFlashes(array $keys)
	{
		$_SESSION['flashes_old'] = array_diff($_SESSION['flashes_old'], $keys);
	}

	// -----------------

	protected function loadSession(): void
	{
		if ($this->loaded === false) {
			session_set_cookie_params([
				'lifetime' => 0,
				'path' => '/',
				'domain' => cookie_domain(),
				'httponly' => true,
				'secure' => true,
				'samesite' => 'Lax',
			]);

			session_name('__Secure-'.$this->cookie_name);

			if (session_start()) {
				if (isset($_SESSION['data']) === false) {
					$_SESSION['data'] = [];
				}

				foreach ($_SESSION['flashes_old'] ?? [] as $key) {
					unset($_SESSION['data'][$key]);
				}

				$_SESSION['flashes_old'] = $_SESSION['flashes'] ?? [];
				$_SESSION['flashes'] = [];

				$this->loaded = true;
			}
		}
	}

}