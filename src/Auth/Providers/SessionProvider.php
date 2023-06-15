<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Auth\Providers;

use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Auth\Interfaces\SessionAuthentication;
use Rovota\Core\Auth\Session;
use Rovota\Core\Auth\TrustedClient;
use Rovota\Core\Auth\User;
use Rovota\Core\Cookie\CookieManager;
use Rovota\Core\Facades\Cookie;
use Rovota\Core\Facades\Registry;
use Rovota\Core\Http\RequestManager;
use Rovota\Core\Kernel\ExceptionHandler;
use Throwable;

class SessionProvider extends Provider implements SessionAuthentication
{

	protected Session|null $session = null;

	// -----------------

	public function getSession(): Session|null
	{
		return $this->session;
	}

	public function hasSession(): bool
	{
		return $this->session instanceof Session;
	}

	public function hasVerifiedSession(): bool
	{
		return $this->session instanceof Session && $this->session->verified;
	}

	public function createSession(array $attributes = []): Session
	{
		if ($this->user()->hasTwoFactorMethods() === false) {
			$attributes['verified'] = true;
		}

		$session = Session::createUsing($this->identity, $attributes);
		if ($session->save()) {
			$this->session = $session;
		}

		return $session;
	}

	public function verifySession(): void
	{
		if ($this->session instanceof Session) {
			$this->session->verified = true;
			$this->session->save();
		}
	}

	public function expireSession(string|null $hash = null): void
	{
		if ($hash === null) {
			if ($this->session instanceof Session) {
				$this->session->expire();
			}
		} else {
			try {
				Session::where(['hash' => $hash])->update(['expiration' => now()]);
			} catch (Throwable $throwable) {
				ExceptionHandler::addThrowable($throwable);
			}
		}
	}

	public function setSessionCookie(Session $session): void
	{
		$name = Registry::string('auth_session_name', 'account');
		CookieManager::queue($name, $session->hash, ['expires' => $session->expiration]);
	}

	public function expireSessionCookie(): void
	{
		CookieManager::expire(Registry::string('auth_session_name', 'account'));
	}

	// -----------------

	public function authenticate(): bool
	{
		$this->loadTrustedClient();

		$cookie = Cookie::findReceived(Registry::string('auth_session_name', 'account'));
		if ($cookie !== null) {
			if (mb_strlen($cookie->value) !== 80) {
				return !$cookie->expire();
			}
			try {
				$session = Session::where(['hash' => $cookie->value, 'ip' => RequestManager::getRequest()->ip()])->where('expiration', '>=', now())->first();
				if ($session instanceof Session) {
					$user = User::find($session->user_id, retention: 0);
					if ($user instanceof User && $user->isSuspended() === false) {
						$this->identity = $user;
						$user->eventAuthenticated();
						$this->session = $session;
						return true;
					}
					$session->expire();
				}
			} catch (Throwable $throwable) {
				ExceptionHandler::addThrowable($throwable);
			}
			$cookie->expire();
		}
		return false;
	}

	// -----------------

	public function attempt(array $credentials): bool
	{
		$username = $credentials['username'];
		$password = $credentials['password'];

		try {
			$user = User::find($username, 'username');
			if ($user instanceof User && $user->verifyPassword($password)) {
				if ($user->isSuspended() === false) {
					$this->identity = $user;
					$user->eventAuthenticated();
					$this->createSession();
					return true;
				}
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
		}

		return false;
	}

	public function login(Identity $identity, array $attributes = []): bool
	{
		$this->identity = $identity;

		$session = $this->createSession($attributes);
		$this->setSessionCookie($session);

		return true;
	}

	public function logout(): bool
	{
		$this->expireSession();
		$this->expireSessionCookie();
		return true;
	}

	public function verify(): bool
	{
		if ($this->hasSession()) {
			$this->verifySession();
			return true;
		}
		return false;
	}

	// -----------------

	public function validate(array $credentials): Identity|false
	{
		$username = $credentials['username'];
		$password = $credentials['password'];

		try {
			$user = User::find($username, 'username');
			if ($user instanceof User && $user->verifyPassword($password)) {
				return $user;
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
		}

		return false;
	}

	// -----------------

	public function trustClient(array $attributes = [], Identity|null $identity = null): void
	{
		$attributes['expiration'] = $attributes['expiration'] ?? now()->addDays(30);
		$client = TrustedClient::createUsing($identity ?? $this->identity, $attributes);

		if ($client->save()) {
			CookieManager::queue('trusted_client', $client->hash, ['expires' => $attributes['expiration']]);
		}

		$this->trusted_client = $client;
	}

	// -----------------

	protected function loadTrustedClient(): void
	{
		$cookie = CookieManager::findReceived('trusted_client');
		if ($cookie !== null) {
			if (mb_strlen($cookie->value) !== 80) {
				$cookie->expire();
			} else {
				try {
					$trusted_client = TrustedClient::where(['hash' => $cookie->value, 'ip' => RequestManager::getRequest()->ip()])->first();
					if ($trusted_client instanceof TrustedClient && ($trusted_client->expiration === null || $trusted_client->expiration->isFuture())) {
						$this->trusted_client = $trusted_client;
					} else {
						$cookie->expire();
					}
				} catch (Throwable $throwable) {
					ExceptionHandler::addThrowable($throwable);
				}
			}
		}
	}

}