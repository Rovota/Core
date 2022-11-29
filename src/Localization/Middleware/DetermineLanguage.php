<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Localization\Middleware;

use Rovota\Core\Auth\AuthManager;
use Rovota\Core\Cookie\Cookie;
use Rovota\Core\Cookie\CookieManager;
use Rovota\Core\Http\Request;
use Rovota\Core\Localization\LocalizationManager;
use Rovota\Core\Support\Str;

class DetermineLanguage
{

	protected string|null $locale = null;

	// -----------------

	public function handle(Request $request): void
	{
		// Attempt to get a value from a cookie
		$cookie = CookieManager::findReceived('locale');
		if ($cookie instanceof Cookie && LocalizationManager::hasLanguage($cookie->value)) {
			$this->locale = $cookie->value;
		}

		// Attempt to get a value from a query parameter
		if ($request->query->has('locale') && Str::contains($request->referrer() ?? '', $request->targetHost())) {
			$locale = trim($request->query->get('locale'));
			if (LocalizationManager::hasLanguage($locale)) {
				$this->locale = $locale;
				CookieManager::queue('locale', $locale, ['expires' => now()->addYear()]);
				if (AuthManager::activeProvider()->check()) {
					$identity = AuthManager::activeProvider()->identity();
					if ($identity->language->locale !== $locale) {
						$identity->setLanguage($locale, true);
					}
				}
			}
		}

		// Attempt to get a value from an identity
		if (AuthManager::activeProvider()->check()) {
			$identity = AuthManager::activeProvider()->identity();
			if (LocalizationManager::hasLanguage($identity->getLanguage()->id)) {
				$this->locale = $identity->getLanguage()->locale;
			}
			$identity_timezone = $identity->meta('timezone');
			if ($identity_timezone !== null) {
				LocalizationManager::setActiveTimezone($identity_timezone);
			}
		}

		// -----------------

		LocalizationManager::loadSourcesUsingIdentifier($this->locale);
	}

}