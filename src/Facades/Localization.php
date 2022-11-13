<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use DateTimeZone;
use Rovota\Core\Localization\Language;
use Rovota\Core\Localization\LocalizationManager;
use Rovota\Core\Support\Bucket;
use Rovota\Core\Support\Collection;

final class Localization
{

	protected function __construct()
	{
	}

	// -----------------
	// Languages

	public static function setActiveLanguage(string|int $identifier): void
	{
		LocalizationManager::setActiveLanguage($identifier);
	}

	public static function getActiveLanguage(): Language
	{
		return LocalizationManager::getActiveLanguage();
	}

	public static function isActiveLanguage(string|int $identifier): bool
	{
		return LocalizationManager::isActiveLanguage($identifier);
	}

	public static function hasLanguage(string|int $identifier): bool
	{
		return LocalizationManager::hasLanguage($identifier);
	}

	public static function getLanguage(string|int $identifier): Language|null
	{
		return LocalizationManager::getLanguage($identifier);
	}

	/**
	 * @returns Collection<int, Language>
	 */
	public static function getLanguages(): Collection
	{
		return LocalizationManager::getLanguages();
	}

	/**
	 * @returns Collection<int, Language>
	 */
	public static function getLanguagesWithPrefix(string $prefix): Collection
	{
		return LocalizationManager::getLanguagesWithPrefix($prefix);
	}

	// -----------------
	// Timezones

	public static function setActiveTimezone(string $identifier): void
	{
		LocalizationManager::setActiveTimezone($identifier);
	}

	public static function getActiveTimezone(): DateTimeZone
	{
		return LocalizationManager::getActiveTimezone();
	}

	public static function hasTimezone(string $identifier): bool
	{
		return LocalizationManager::hasTimezone($identifier);
	}

	public static function getTimezone(string $identifier): DateTimeZone|null
	{
		return LocalizationManager::getTimezone($identifier);
	}

	public static function getTimezones(): array
	{
		return LocalizationManager::getTimezones();
	}

	public static function getTimezonesWithPrefix(string $prefix): array
	{
		return LocalizationManager::getTimezonesWithPrefix($prefix);
	}

	// -----------------
	// Sources

	public static function addSource(string $name, string $location): void
	{
		LocalizationManager::addSource($name, $location);
	}

	public static function addToSource(string $name, string $location): void
	{
		LocalizationManager::addToSource($name, $location);
	}

	public static function getSource(string $name): array|null
	{
		return LocalizationManager::getSource($name);
	}

	public static function getSources(): array
	{
		return LocalizationManager::getSources();
	}

	public static function removeSource(string $name): void
	{
		LocalizationManager::removeSource($name);
	}

	public static function flushSource(string $name): void
	{
		LocalizationManager::flushSource($name);
	}

	public static function reloadSource(string $name): void
	{
		LocalizationManager::reloadSource($name);
	}

	public static function reloadSources(array $names = []): void
	{
		LocalizationManager::reloadSources($names);
	}

	public static function setDefaultSource(string $name): void
	{
		LocalizationManager::setDefaultSource($name);
	}

	public static function getDefaultSource(): string
	{
		return LocalizationManager::getDefaultSource();
	}

	// -----------------
	// Strings

	public static function getStrings(array $sources = []): array
	{
		return LocalizationManager::getStrings($sources);
	}

	public static function getStringTranslation(string $string, string|null $source = null): string
	{
		return LocalizationManager::getStringTranslation($string, $source);
	}

	// -----------------
	// Formats

	public static function getFormats(): Bucket
	{
		return LocalizationManager::getFormats();
	}

	public static function getFormatsByLocale(string $locale): Bucket
	{
		return LocalizationManager::getFormatsByLocale($locale);
	}

}