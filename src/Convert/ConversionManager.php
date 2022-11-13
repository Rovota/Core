<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Rovota\Core\Convert\Exceptions\MissingLanguageException;
use Rovota\Core\Kernel\ExceptionHandler;

final class ConversionManager
{

	protected static array $accent_map = [];

	protected static array $conversions = [];

	protected static array $languages = [];

	protected static string $default_language = '';

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 * @throws \Rovota\Core\Convert\Exceptions\MissingLanguageException
	 */
	public static function initialize(): void
	{
		if (self::hasLanguage('md_cm') === false) {
			self::addLanguage('md_cm', 'Markdown (CommonMark)');
		}
		if (self::hasLanguage('md_gfm') === false) {
			self::addLanguage('md_gfm', 'Markdown (GitHub)');
		}

		$converter_commonmark = new CommonMarkConverter([
			'html_input' => 'strip',
			'allow_unsafe_links' => false,
		]);

		$converter_github = new GithubFlavoredMarkdownConverter([
			'html_input' => 'strip',
			'allow_unsafe_links' => false,
		]);

		self::addConversion('default', function ($string) use ($converter_commonmark) {
			return $converter_commonmark->convert($string);
		}, 'md_cm');

		self::addConversion('default', function ($string) use ($converter_github) {
			return $converter_github->convert($string);
		}, 'md_gfm');
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Convert\Exceptions\MissingLanguageException
	 */
	public static function addConversion(string $name, callable $function, string|null $language = null): void
	{
		$language = $language ?? self::$default_language;
		if (key_exists($language, self::$languages)) {
			self::$conversions[$language][$name] = $function;
		} else {
			throw new MissingLanguageException("Conversions cannot be added to the undefined language '$name'.");
		}
	}

	public static function removeConversion(string $name, string|null $language = null): void
	{
		$language = $language ?? self::$default_language;
		unset(self::$conversions[$language][$name]);
	}

	public static function hasConversion(string $name, string|null $language = null): bool
	{
		$language = $language ?? self::$default_language;
		return key_exists($name, self::$conversions[$language]);
	}

	public static function clearConversions(string|null $language = null): void
	{
		$language = $language ?? self::$default_language;
		self::$conversions[$language] = [];
	}

	// -----------------

	public static function addLanguage(string $name, string $label): void
	{
		self::$languages[$name] = trim($label);
		self::$conversions[$name] = [];

		if (self::$default_language === '') {
			self::setDefaultLanguage($name);
		}
	}

	public static function getLanguageLabel(string $name): string|null
	{
		return self::$languages[$name] ?? null;
	}

	public static function removeLanguage(string $name): void
	{
		unset(self::$languages[$name]);
		unset(self::$conversions[$name]);
	}

	public static function hasLanguage(string $name): bool
	{
		return key_exists($name, self::$languages);
	}

	public static function getLanguages(): array
	{
		return self::$languages;
	}

	public static function setDefaultLanguage(string $name): void
	{
		if (key_exists($name, self::$languages)) {
			self::$default_language = $name;
		} else {
			ExceptionHandler::logMessage('warning', "Undefined languages cannot be set as default: '{name}'.", ['name' => $name]);
		}
	}

	public static function getDefaultLanguage(): string
	{
		return self::$default_language;
	}

	public static function getDefaultLanguageLabel(): string|null
	{
		return self::$languages[self::$default_language] ?? null;
	}

	// -----------------

	public static function toHtml(string $string, string|null $language = null): string
	{
		$string = trim($string);
		$language = $language ?? self::$default_language;

		if (!key_exists($language, self::$languages)) {
			return $string;
		}

		foreach (self::$conversions[$language] as $callable) {
			$string = $callable($string);
		}

		return $string;
	}

	// -----------------

	public static function textToBytes(string $size): int
	{
		if (strlen($size) === 0) {
			return 0;
		}

		$size = strtolower($size);
		$max = ltrim($size, '+');

		$max = match (true) {
			str_starts_with($max, '0x') => intval($max, 16),
			str_starts_with($max, '0') => intval($max, 8),
			default => (int)$max
		};

		switch (substr($size, -1)) {
			case 't':
				$max *= 1024;
			// no break
			case 'g':
				$max *= 1024;
			// no break
			case 'm':
				$max *= 1024;
			// no break
			case 'k':
				$max *= 1024;
		}

		return $max;
	}

	// -----------------

	public static function toAscii(string $string): string
	{
		if (empty(self::$accent_map)) {
			self::loadAccentMap();
		}
		$string = strtr($string, self::$accent_map);
		return trim($string);
	}

	protected static function loadAccentMap(): void
	{
		self::$accent_map = include 'accent_map.php';
	}

}