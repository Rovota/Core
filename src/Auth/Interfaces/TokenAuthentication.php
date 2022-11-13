<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Auth\Interfaces;

use Rovota\Core\Auth\ApiToken;

interface TokenAuthentication
{

	public function getToken(): ApiToken|null;

	public function hasToken(): bool;

	public function createToken(array $attributes = []): ApiToken;

	public function expireToken(string|null $token = null): void;

}