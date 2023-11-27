<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Localization;

use DateTimeZone;
use Rovota\Core\Facades\Registry;
use Rovota\Core\Http\RequestManager;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Enums\Status;
use Throwable;

/**
 * @internal
 */
final class LocaleDataManager
{

	protected static array $locales = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function load(string $locale): void
	{
		$data = new Bucket();
		$file = __DIR__.'/data/locales/'.$locale.'.php';
		if (file_exists($file)) {
			$data->import(include $file);
		} else {
			$data->import(include __DIR__ . '/data/locales/base.php');
		}

		self::$locales[$locale] = $data;
	}

	// -----------------

	public static function get(string|null $locale = null): Bucket|null
	{
		if ($locale === null) {
			$locale = LocalizationManager::getActiveLanguage()->locale;
		}
		if (!isset(self::$locales[$locale])) {
			self::load($locale);
		}
		return self::$locales[$locale];
	}

	/**
	 * @returns array<string, Bucket>
	 */
	public static function all(): array
	{
		return self::$locales;
	}

}