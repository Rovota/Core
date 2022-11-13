<?php
/** @noinspection DuplicatedCode */

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Views;

use Rovota\Core\Addon\AddonManager;
use Rovota\Core\Facades\Localization;
use Rovota\Core\Facades\Registry;
use Rovota\Core\Localization\Language;
use Rovota\Core\Support\MessageBucket;
use Rovota\Core\Views\Components\Meta;
use Rovota\Core\Views\Components\Script;
use Rovota\Core\Views\Components\Style;
use Rovota\Core\Views\Exceptions\MissingViewException;

/**
 * @internal
 */
final class ViewManager
{
	/**
	 * @var array<string, string>
	 */
	protected static array $views = [];

	protected static MessageBucket $errors;

	/**
	 * @var array<string, array<string, Style>>
	 */

	protected static array $styles = [];
	/**
	 * @var array<string, array<string, Script>>
	 */
	protected static array $scripts = [];

	/**
	 * @var array<string, array<string, mixed>>
	 */
	protected static array $variables = [];

	/**
	 * @var array<string, array<string, Meta>>
	 */
	protected static array $meta = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		self::$errors = new MessageBucket();

		self::addMeta('*', 'application-name', ['name' => 'application-name', 'content' => Registry::string('site_name')]);
		self::addMeta('*', 'description', ['name' => 'description', 'content' => Registry::string('site_description')]);
		self::addMeta('*', 'keywords', ['name' => 'keywords', 'content' => Registry::string('site_keywords')]);
		self::addMeta('*', 'author', ['name' => 'author', 'content' => Registry::string('site_author')]);

		self::setOpenGraphTags();
		self::setTwitterTags();

		if (Registry::bool('enable_rovota_branding')) {
			self::addMeta('*', 'generator', [
				'name' => 'generator', 'content' => Registry::string('site_generator')
			]);
		}
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Views\Exceptions\MissingViewException
	 * @noinspection DuplicatedCode
	 */
	public static function make(string $name, string|null $source): View
	{
		if (isset(self::$views[$name]) && $source === null) {
			$name = self::$views[$name];
		}

		if (str_contains($name, '\\')) {
			return new $name(null, [
				'styles' => array_merge(self::$styles['*'] ?? [], self::$styles[$name] ?? []),
				'scripts' => array_merge(self::$scripts['*'] ?? [], self::$scripts[$name] ?? []),
				'variables' => array_merge(self::$variables['*'] ?? [], self::$variables[$name] ?? []),
				'meta' => array_merge(self::$meta['*'] ?? [], self::$meta[$name] ?? []),
			],  self::$errors);
		}

		$layout = self::getMatchingLayout($name, $source);

		return new View($layout, [
			'styles' => array_merge(self::$styles['*'] ?? [], self::$styles[$name] ?? []),
			'scripts' => array_merge(self::$scripts['*'] ?? [], self::$scripts[$name] ?? []),
			'variables' => array_merge(self::$variables['*'] ?? [], self::$variables[$name] ?? []),
			'meta' => array_merge(self::$meta['*'] ?? [], self::$meta[$name] ?? []),
		],  self::$errors);
	}

	public static function register(string $name, string $class): void
	{
		self::$views[$name] = $class;
	}

	public static function isRegistered(string $name, string|null $class = null): bool
	{
		if (isset(self::$views[$name]) === false) {
			return false;
		}

		if ($class !== null && self::$views[$name] !== $class) {
			return false;
		}

		return true;
	}

	/**
	 * @throws \Rovota\Core\Views\Exceptions\MissingViewException
	 */
	public static function makeClean(string $name, string|null $source): View
	{
		$layout = self::getMatchingLayout($name, $source);
		return new View($layout, [], new MessageBucket());
	}

	/**
	 * @throws \Rovota\Core\Views\Exceptions\MissingViewException
	 */
	public static function makeMail(string $name, string|null $source, Language $language): View
	{
		$layout = self::getMatchingMailLayout($name, $source, $language);
		return new View($layout, [], new MessageBucket());
	}

	// -----------------

	public static function hasStyle(string $view, string $identifier): bool
	{
		return isset(self::$styles[$view][$identifier]);
	}

	public static function addStyle(array|string $views, string $identifier, Style|array $attributes): Style
	{
		$views = is_array($views) ? $views : [$views];
		$style = $attributes instanceof Style ? $attributes : new Style($attributes);

		foreach ($views as $view) {
			if (isset(self::$views[$view])) {
				$view = self::$views[$view];
			}
			self::$styles[$view][$identifier] = $style;
		}

		return $style;
	}

	public static function updateStyle(array|string $views, string $identifier, array $attributes): void
	{
		$views = is_array($views) ? $views : [$views];
		foreach ($views as $view) {
			if (isset(self::$views[$view])) {
				$view = self::$views[$view];
			}
			self::$styles[$view][$identifier]?->setAttributes($attributes);
		}
	}

	public static function removeStyle(string $view, string $identifier): void
	{
		unset(self::$styles[$view][$identifier]);
	}

	// -----------------

	public static function hasScript(string $view, string $identifier): bool
	{
		return isset(self::$scripts[$view][$identifier]);
	}

	public static function addScript(array|string $views, string $identifier, Script|array $attributes): Script
	{
		$views = is_array($views) ? $views : [$views];
		$script = $attributes instanceof Script ? $attributes : new Script($attributes);

		foreach ($views as $view) {
			if (isset(self::$views[$view])) {
				$view = self::$views[$view];
			}
			self::$scripts[$view][$identifier] = $script;
		}

		return $script;
	}

	public static function updateScript(array|string $views, string $identifier, array $attributes): void
	{
		$views = is_array($views) ? $views : [$views];
		foreach ($views as $view) {
			if (isset(self::$views[$view])) {
				$view = self::$views[$view];
			}
			self::$scripts[$view][$identifier]?->setAttributes($attributes);
		}
	}

	public static function removeScript(string $view, string $identifier): void
	{
		unset(self::$scripts[$view][$identifier]);
	}

	// -----------------

	public static function hasVariable(string $view, string $name): bool
	{
		return isset(self::$variables[$view][$name]);
	}

	public static function addVariable(array|string $views, string $name, mixed $value): void
	{
		$views = is_array($views) ? $views : [$views];
		foreach ($views as $view) {
			if (isset(self::$views[$view])) {
				$view = self::$views[$view];
			}
			self::$variables[$view][$name] = $value;
		}
	}

	public static function updateVariable(array|string $views, string $name, mixed $value): void
	{
		$views = is_array($views) ? $views : [$views];
		foreach ($views as $view) {
			if (isset(self::$views[$view])) {
				$view = self::$views[$view];
			}
			if (is_array($value)) {
				foreach ($value as $key => $item) {
					self::$variables[$view][$name][$key] = $item;
				}
			} else {
				self::$variables[$view][$name] = $value;
			}
		}
	}

	public static function removeVariable(string $view, string $name): void
	{
		unset(self::$variables[$view][$name]);
	}

	// -----------------

	public static function hasMeta(string $view, string $identifier): bool
	{
		return isset(self::$meta[$view][$identifier]);
	}

	public static function addMeta(array|string $views, string $identifier, Meta|array $attributes): Meta
	{
		$views = is_array($views) ? $views : [$views];
		$meta = $attributes instanceof Meta ? $attributes : new Meta($attributes);

		foreach ($views as $view) {
			if (isset(self::$views[$view])) {
				$view = self::$views[$view];
			}
			self::$meta[$view][$identifier] = $meta;
		}

		return $meta;
	}

	public static function updateMeta(array|string $views, string $identifier, array $attributes): void
	{
		$views = is_array($views) ? $views : [$views];
		foreach ($views as $view) {
			if (isset(self::$views[$view])) {
				$view = self::$views[$view];
			}
			self::$meta[$view][$identifier]?->setAttributes($attributes);
		}
	}

	public static function removeMeta(string $view, string $identifier): void
	{
		unset(self::$meta[$view][$identifier]);
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Views\Exceptions\MissingViewException
	 */
	protected static function getMatchingLayout(string $name, string|null $source = null): string
	{
		$path = sprintf('/templates/views/%s.php', $name);

		if ($source !== null) {
			if (file_exists(base_path($source).$path)) {
				return $source.$path;
			} else {
				throw new MissingViewException("View could not be found: $source$path.");
			}
		} else {
			if (AddonManager::isThemeEnabled() === true) {
				$source = '/themes/'.AddonManager::getTheme()->name;
				if (file_exists(base_path($source).$path)) {
					return $source.$path;
				}
			}

			// Attempt to use a fallback when above fails.
			$source = '/vendor/rovota/core/src/Web';
			if (file_exists(base_path($source).$path)) {
				return $source.$path;
			} else {
				throw new MissingViewException("View could not be found. Fallback not available for: $name");
			}
		}
	}

	/**
	 * @throws \Rovota\Core\Views\Exceptions\MissingViewException
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

	// -----------------

	protected static function setOpenGraphTags(): void
	{
		self::addMeta('*', 'og:site_name', ['property' => 'og:site_name', 'content' => Registry::string('site_name')]);
		self::addMeta('*', 'og:locale', ['property' => 'og:locale', 'content' => Localization::getActiveLanguage()->locale]);
		self::addMeta('*', 'og:type', ['property' => 'og:type', 'content' => 'website']);

		self::addMeta('*', 'og:title', ['property' => 'og:title', 'content' => Registry::string('site_name')]);
		self::addMeta('*', 'og:description', ['property' => 'og:description', 'content' => Registry::string('site_description')]);
		self::addMeta('*', 'og:url', ['property' => 'og:url', 'content' => request()->fullUrl()]);

		self::addMeta('*', 'og:image', ['property' => 'og:image', 'content' => Registry::string('site_image')]);
		self::addMeta('*', 'og:image:secure_url', ['property' => 'og:image:secure_url', 'content' => Registry::string('site_image')]);
	}

	protected static function setTwitterTags(): void
	{
		self::addMeta('*', 'twitter:title', ['name' => 'twitter:title', 'content' => Registry::string('site_name')]);
		self::addMeta('*', 'twitter:description', ['name' => 'twitter:description', 'content' => Registry::string('site_description')]);
		self::addMeta('*', 'twitter:url', ['name' => 'twitter:url', 'content' => request()->fullUrl()]);
		self::addMeta('*', 'twitter:image', ['name' => 'twitter:image', 'content' => Registry::string('site_image')]);

		self::addMeta('*', 'twitter:card', ['name' => 'twitter:card', 'content' => 'summary_large_image']);
	}

}