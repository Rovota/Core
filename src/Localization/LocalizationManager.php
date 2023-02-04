<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Localization;

use DateTimeZone;
use Rovota\Core\Facades\Registry;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Enums\Status;
use Throwable;

/**
 * @internal
 */
final class LocalizationManager
{
	/**
	 * @var Bucket<int, Language>
	 */
	protected static Bucket $languages;

	protected static array $locales = [];
	protected static string $active_locale;

	protected static array $timezones = [];
	protected static string $active_timezone;

	protected static array $sources = [];
	protected static string $default_source;

	protected static array $loaded = [];

	protected static Bucket $formats;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		try {
			self::$languages = Language::where(['status' => Status::Enabled])->orderBy('label')->getBy('id');
			foreach (self::$languages as $id => $language) {
				self::$locales[$language->locale] = $id;
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable, true);
		}

		self::$timezones = timezone_identifiers_list();
		self::$active_locale = request()->prefersLocale(array_keys(self::$locales), Registry::string('default_locale', 'en_US'));
		self::$active_timezone = Registry::string('default_timezone', ini_get('date.timezone'));
		self::$default_source = 'core';

		self::$formats = self::loadFormatsByLocale(self::$active_locale);
		self::addSource('core', 'vendor/Rovota/Core/src/Localization');
	}

	// -----------------
	// Languages

	public static function setActiveLanguage(string|int $identifier): void
	{
		$language = self::findLanguageByIdentifier($identifier);
		if ($language instanceof Language) {
			self::$active_locale = $language->locale;
		}

		foreach (array_keys(self::$sources) as $source) {
			self::loadStringsFromSource($source);
		}

		self::$formats = self::loadFormatsByLocale(self::$active_locale);
	}

	public static function getActiveLanguage(): Language
	{
		return self::$languages[self::$locales[self::$active_locale]];
	}

	public static function isActiveLanguage(string|int $identifier): bool
	{
		$language = self::findLanguageByIdentifier($identifier);
		return $language instanceof Language && self::$active_locale === $language->locale;
	}

	public static function hasLanguage(string|int $identifier): bool
	{
		return self::findLanguageByIdentifier($identifier) instanceof Language;
	}

	public static function getLanguage(string|int $identifier): Language|null
	{
		return self::findLanguageByIdentifier($identifier);
	}

	/**
	 * @returns Bucket<int, Language>
	 */
	public static function getLanguages(): Bucket
	{
		return self::$languages;
	}

	/**
	 * @returns Bucket<int, Language>
	 */
	public static function getLanguagesWithPrefix(string $prefix): Bucket
	{
		return self::$languages->filter(function (Language $language) use ($prefix) {
			return str_starts_with($language->locale, $prefix);
		});
	}

	// -----------------
	// Timezones

	public static function setActiveTimezone(string $identifier): void
	{
		if (in_array($identifier, self::$timezones)) {
			self::$active_timezone = $identifier;
		}
	}

	public static function getActiveTimezone(): DateTimeZone
	{
		return timezone_open(self::$active_timezone);
	}

	public static function hasTimezone(string $identifier): bool
	{
		return in_array($identifier, self::$timezones);
	}

	public static function getTimezone(string $identifier): DateTimeZone|null
	{
		return self::hasTimezone($identifier) ? timezone_open($identifier) : null;
	}

	public static function getTimezones(): array
	{
		return self::$timezones;
	}

	public static function getTimezonesWithPrefix(string $prefix): array
	{
		return as_bucket(self::$timezones)->filter(function ($timezone) use ($prefix) {
			return str_starts_with($timezone, $prefix);
		})->values()->toArray();
	}

	// -----------------
	// Sources

	public static function addSource(string $name, string $location): void
	{
		self::$sources[$name] = [$location];
	}

	public static function addToSource(string $name, string $location): void
	{
		if (in_array($location, self::$sources[$name]) === false) {
			self::$sources[$name][] = $location;
		}
	}

	public static function getSource(string $name): array|null
	{
		return self::$sources[$name] ?? null;
	}

	public static function getSources(): array
	{
		return self::$sources;
	}

	public static function removeSource(string $name): void
	{
		if (self::$default_source === $name) {
			ExceptionHandler::logMessage('warning', "The source '{name}' cannot be removed when set as the default source. ", ['name' => $name]);
		}
		unset(self::$sources[$name]);
		unset(self::$loaded[$name]);
	}

	public static function flushSource(string $name): void
	{
		if (isset(self::$sources[$name])) {
			self::$loaded[$name] = [];
		}
	}

	public static function reloadSource(string $name): void
	{
		self::flushSource($name);
		self::loadStringsFromSource($name);
	}

	public static function reloadSources(array $names = []): void
	{
		if (empty($names)) {
			foreach (array_keys(self::$sources) as $name) {
				self::reloadSource($name);
			}
		} else {
			foreach ($names as $name) {
				self::reloadSource($name);
			}
		}
	}

	public static function setDefaultSource(string $name): void
	{
		if (isset(self::$sources[$name])) {
			self::$default_source = $name;
		}
	}

	public static function getDefaultSource(): string
	{
		return self::$default_source;
	}

	// -----------------
	// Strings

	public static function getStrings(array $sources = []): array
	{
		if (empty($sources)) {
			return self::$loaded;
		}
		return as_bucket(self::$sources)->only($sources)->toArray();
	}

	public static function getStringTranslation(string $string, string|null $source = null): string
	{
		if ($source === null) {
			$source = self::$default_source;
		}
		if (isset(self::$sources[$source]) && !empty(self::$loaded[$source][$string])) {
			return self::$loaded[$source][$string];
		}
		return $string;
	}

	// -----------------
	// Formats

	public static function getFormats(): Bucket
	{
		return self::$formats;
	}

	public static function getFormatsByLocale(string $locale): Bucket
	{
		return self::loadFormatsByLocale($locale);
	}

	// -----------------
	// Middleware helpers

	public static function loadSourcesUsingIdentifier(string|int|null $identifier): void
	{
		if ($identifier !== null) {
			$language = self::findLanguageByIdentifier($identifier);
			if ($language instanceof Language) {
				self::$active_locale = $language->locale;
			}
		}

		foreach (array_keys(self::$sources) as $source) {
			self::loadStringsFromSource($source);
		}

		self::$formats = self::loadFormatsByLocale(self::$active_locale);
	}

	// -----------------

	protected static function loadStringsFromSource(string $name): void
	{
		$results = [];
		foreach (self::$sources[$name] as $location) {
			$location = sprintf('%s/translations/%s.json', $location, self::$active_locale);
			if (file_exists($location)) {
				$file_contents = file_get_contents($location);
				$results = array_merge($results, json_decode($file_contents, true) ?? []);
			}
		}
		self::$loaded[$name] = $results;
	}

	protected static function findLanguageByIdentifier(string|int $identifier): Language|null
	{
		if (is_string($identifier) && isset(self::$locales[$identifier])) {
			$identifier = self::$locales[$identifier];
		}
		return self::$languages[$identifier] ?? null;
	}

	protected static function loadFormatsByLocale(string $locale): Bucket
	{
		$formats = new Bucket();
		$file = __DIR__.'/data/'.$locale.'.php';
		if (file_exists($file)) {
			$formats->import(include $file);
		} else {
			$formats->import(include __DIR__.'/data/base.php');
		}
		return $formats;
	}

}