<?php
/** @noinspection DuplicatedCode */

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Views;

use Rovota\Core\Localization\Language;

/**
 * @internal
 */
final class ViewManager
{
	// -----------------

	/**
	 * @throws MissingViewException
	 */
	public static function makeMail(string $name, string|null $source, Language $language): View
	{
		$layout = self::getMatchingMailLayout($name, $source, $language);
		return new View($layout, []);
	}

	// -----------------

	/**
	 * @throws MissingViewException
	 */
	protected static function getMatchingMailLayout(string $name, string|null $source, Language $language): string
	{
		if ($source === null) {
			$source = AddonManager::isThemeEnabled() ? sprintf('themes/%s', AddonManager::getTheme()->name) : 'vendor/rovota/core/src/Web';
		}

		$path = sprintf('/templates/views/mail/%s/%s.php', $language->locale, $name);

		// Try given locale
		if (file_exists(base_path($source).$path)) {
			return $source.$path;
		}

		// Try default locale
		$path = sprintf('/templates/views/mail/%s/%s.php', Registry::string('default_locale', 'en_US'), $name);
		if (file_exists(base_path($source).$path)) {
			return $source.$path;
		}

		// Try English (US)
		$path = sprintf('/templates/views/mail/%s/%s.php', 'en_US', $name);
		if (file_exists(base_path($source).$path)) {
			return $source.$path;
		}

		throw new MissingViewException("View could not be found. Fallback not available for: $name");
	}

}