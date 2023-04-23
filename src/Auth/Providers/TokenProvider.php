<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Auth\Providers;

use Rovota\Core\Auth\ApiToken;
use Rovota\Core\Auth\Enums\TokenStatus;
use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Auth\Interfaces\TokenAuthentication;
use Rovota\Core\Auth\TrustedClient;
use Rovota\Core\Auth\User;
use Rovota\Core\Http\RequestManager;
use Rovota\Core\Kernel\ExceptionHandler;
use Throwable;

class TokenProvider extends Provider implements TokenAuthentication
{

	protected ApiToken|null $token = null;

	// -----------------

	public function getToken(): ApiToken|null
	{
		return $this->token;
	}

	public function hasToken(): bool
	{
		return $this->token instanceof ApiToken;
	}

	public function createToken(array $attributes = []): ApiToken
	{
		$token = ApiToken::createUsing($this->identity, $attributes);
		if ($token->save()) {
			$this->token = $token;
		}
		return $token;
	}

	public function expireToken(string|null $token = null): void
	{
		if ($token === null) {
			if ($this->token instanceof ApiToken) {
				$this->token->expire();
			}
		} else {
			try {
				ApiToken::where(['token' => $token])->update(['expiration' => now()]);
			} catch (Throwable $throwable) {
				ExceptionHandler::addThrowable($throwable);
			}
		}
	}

	// -----------------

	public function authenticate(): bool
	{
		$this->loadTrustedClient();

		try {
			$token = ApiToken::where(['token' => RequestManager::getRequest()->authToken() ?? '', 'status' => TokenStatus::Active])->first();
			if ($token instanceof ApiToken && ($token->expiration === null || $token->expiration->isFuture())) {
				return $this->loadIdentityUsingToken($token);
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
		}

		return false;
	}

	// -----------------

	public function attempt(array $credentials): bool
	{
		$token = $credentials['token'];

		try {
			$token = ApiToken::where(['token' => $token, 'status' => TokenStatus::Active])->first();
			if ($token instanceof ApiToken && ($token->expiration === null || $token->expiration->isFuture())) {
				return $this->loadIdentityUsingToken($token);
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
		}

		return false;
	}

	public function login(Identity $identity, array $attributes = []): bool
	{
		return false;
	}

	public function logout(): bool
	{
		$this->expireToken();
		return false;
	}

	// -----------------

	public function validate(array $credentials): Identity|bool
	{
		$token = $credentials['token'];

		try {
			$token = ApiToken::where(['token' => $token, 'status' => TokenStatus::Active])->first();
			if ($token instanceof ApiToken && ($token->expiration === null || $token->expiration->isFuture())) {
				$user = User::find($token->user_id);
				if ($user instanceof User && $user->isSuspended() === false) {
					return $user;
				}
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
		}

		return false;
	}

	// -----------------

	public function trustClient(array $attributes = []): void
	{
		$attributes['expiration'] = $attributes['expiration'] ?? now()->addDays(30);
		$attributes['hash'] = $attributes['hash'] ?? $this->token->token;

		$client = TrustedClient::createUsing($this->identity, $attributes);
		$client->save();

		$this->trusted_client = $client;
	}

	// -----------------

	protected function loadTrustedClient(): void
	{
		try {
			$trusted_client = TrustedClient::where(['hash' => RequestManager::getRequest()->authToken() ?? '', 'ip' => RequestManager::getRequest()->ip()])->first();
			if ($trusted_client instanceof TrustedClient && ($trusted_client->expiration === null || $trusted_client->expiration->isFuture())) {
				$this->trusted_client = $trusted_client;
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
		}
	}

	protected function loadIdentityUsingToken(ApiToken $token): bool
	{
		try {
			$user = User::find($token->user_id, retention: 0);
			if ($user instanceof User && $user->isSuspended() === false) {
				$this->identity = $user;
				$user->eventAuthenticated();
				$this->token = $token;
				return true;
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
		}
		return false;
	}

}