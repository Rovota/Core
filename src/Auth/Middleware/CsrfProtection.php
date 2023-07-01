<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Auth\Middleware;

use Rovota\Core\Auth\AccessManager;
use Rovota\Core\Cookie\CookieManager;
use Rovota\Core\Http\Enums\StatusCode;
use Rovota\Core\Http\Request;

class CsrfProtection
{
	/**
	 * Reject requests that are using the POST method, but do not specify a CSRF token.
	 */
	public function handle(Request $request): void
	{
		$token_name = AccessManager::getCsrfTokenName();
		$token_value = AccessManager::getCsrfToken();

		if ($request->isPost() && $request->post->get($token_name) !== $token_value) {
			echo response(StatusCode::Forbidden); exit;
		}

		if (CookieManager::isReceived($token_name) === false) {
			CookieManager::queue($token_name, $token_value);
		}
	}

}