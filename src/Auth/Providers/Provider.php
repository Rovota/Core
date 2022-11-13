<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Auth\Providers;

use Rovota\Core\Auth\Interfaces\AuthProvider;
use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Auth\TrustedClient;
use Rovota\Core\Auth\User;

abstract class Provider implements AuthProvider
{

	protected Identity|null $identity = null;

	protected TrustedClient|null $trusted_client = null;

	protected array $config = [];

	// -----------------

	function __construct()
	{
	}

	// -----------------

	public function identity(): Identity|null
	{
		return $this->identity;
	}

	public function user(): User|null
	{
		return $this->identity instanceof User ? $this->identity : null;
	}

	public function id(): int|string|null
	{
		return $this->identity?->getId();
	}

	// -----------------

	public function check(): bool
	{
		return $this->identity instanceof Identity;
	}

	public function guest(): bool
	{
		return $this->identity === null;
	}

	// -----------------

	public function set(Identity $identity): void
	{
		$this->identity = $identity;
	}

	// -----------------

	public function isClientTrusted(array $attributes = []): bool
	{
		if ($this->trusted_client === null) {
			return false;
		}

		foreach ($attributes as $name => $value) {
			if ($this->trusted_client->{$name} !== $value) return false;
		}

		return true;
	}

}