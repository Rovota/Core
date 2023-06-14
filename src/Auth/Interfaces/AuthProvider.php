<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Auth\Interfaces;

use Rovota\Core\Auth\User;

interface AuthProvider
{

	public function identity(): Identity|null;

	public function user(): User|null;

	public function id(): string|int|null;

	// -----------------

	public function authenticate(): bool;

	public function check(): bool;

	public function guest(): bool;

	// -----------------

	/**
	 * Use a set of credentials to attempt authenticating the identity manually.
	 */
	public function attempt(array $credentials): bool;

	/**
	 * Specify an identity that should be authenticated manually.
	 */
	public function login(Identity $identity, array $attributes = []): bool;

	/**
	 * Sign out an identity manually.
	 */
	public function logout(): bool;

	/**
	 * Verify an identity manually.
	 */
	public function verify(): bool;

	// -----------------

	/**
	 * Validate the given credentials without creating an authenticated state.
	 */
	public function validate(array $credentials): Identity|bool;

	/**
	 * Force a specific identity to be used.
	 */
	public function set(Identity $identity): void;

	// -----------------

	public function trustClient(array $attributes = []): void;

	public function isClientTrusted(array $attributes = []): bool;

}