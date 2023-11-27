<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support;

use NumberFormatter;
use Rovota\Core\Localization\LocaleDataManager;
use Rovota\Core\Localization\LocalizationManager;

final class Number
{

	protected function __construct()
	{
	}

	// -----------------

	public static function format(int|float $number, int $precision = 2, string|null $locale = null): string
	{
		$formatter = NumberFormatter::create(self::getLocale($locale), NumberFormatter::DECIMAL);
		$formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $precision);

		return $formatter->format($number);
	}

	// -----------------

	public static function currency(int|float $amount, string|null $in = null, int $precision = 2, string|null $locale = null): string
	{
		$formatter = NumberFormatter::create(self::getLocale($locale), NumberFormatter::CURRENCY);
		$formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $precision);

		return $formatter->formatCurrency($amount, $in ?? $formatter->getSymbol(NumberFormatter::INTL_CURRENCY_SYMBOL));
	}

	public static function storage(int|float $bytes, int $precision = 2, string|null $locale = null): string
	{
		$data = LocaleDataManager::get(self::getLocale($locale));
		$suffixes = $data->array('units.storage.short');

		$class = min((int)log($bytes, 1024), count($suffixes) - 1);
		$value = self::format($bytes / pow(1024, $class), $precision, $locale);

		return sprintf('%s %s', $value, $suffixes[$class]);
	}

	// -----------------

	protected static function getLocale(string|null $locale): string
	{
		return $locale ?? LocalizationManager::getActiveLanguage()->locale ?? 'en_US';
	}

}