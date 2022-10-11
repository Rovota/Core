<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Auth\Interfaces;

use Rovota\Core\Auth\Session;

interface SessionAuthentication
{

	public function getSession(): Session|null;

	public function hasSession(): bool;

	public function hasVerifiedSession(): bool;

	public function createSession(array $attributes = []): Session;

	public function verifySession(): void;

	public function expireSession(string|null $hash = null): void;

	public function setSessionCookie(Session $session): void;

	public function expireSessionCookie(): void;

}