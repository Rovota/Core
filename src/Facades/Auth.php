<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Auth\AuthManager;
use Rovota\Core\Auth\Interfaces\AuthProvider;
use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Auth\User;

final class Auth
{

	protected function __construct()
	{
	}

	// -----------------

	public static function provider(string $name): AuthProvider
	{
		return AuthManager::get($name);
	}

	public static function activeProvider(): AuthProvider|null
	{
		return AuthManager::activeProvider();
	}

	// -----------------

	public static function identity(): Identity|null
	{
		return AuthManager::activeProvider()?->identity();
	}

	public static function user(): User|null
	{
		return AuthManager::activeProvider()?->user();
	}

	public static function id(): string|int|null
	{
		return AuthManager::activeProvider()?->id();
	}

	// -----------------

	public static function check(): bool
	{
		return AuthManager::activeProvider()?->check() ?? false;
	}

	public static function guest(): bool
	{
		return AuthManager::activeProvider()?->guest() ?? false;
	}

	// -----------------

	public static function attempt(array $credentials): bool
	{
		return AuthManager::activeProvider()?->attempt($credentials) ?? false;
	}

	public static function login(Identity $identity, array $attributes = []): bool
	{
		return AuthManager::activeProvider()?->login($identity, $attributes) ?? false;
	}

	public static function logout(): bool
	{
		return AuthManager::activeProvider()?->logout() ?? false;
	}

	public static function verify(): bool
	{
		return AuthManager::activeProvider()?->verify() ?? false;
	}

	// -----------------

	public static function validate(array $credentials): Identity|bool
	{
		return AuthManager::activeProvider()?->validate($credentials) ?? false;
	}

	public static function set(Identity $identity): void
	{
		AuthManager::activeProvider()?->set($identity);
	}

	// -----------------

	public static function trustClient(array $attributes = [], Identity|null $identity = null): void
	{
		AuthManager::activeProvider()->trustClient($attributes, $identity);
	}

	public static function isClientTrusted(array $attributes = []): bool
	{
		return AuthManager::activeProvider()->isClientTrusted($attributes);
	}

}