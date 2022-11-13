<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Localization\Formatter;
use Rovota\Core\Localization\NumberFormatter;

final class Format
{

	protected function __construct()
	{
	}

	// -----------------

	public static function set(string $key, mixed $value, string|null $locale = null): Formatter
	{
		$formatter = Formatter::create(0, $locale);
		$formatter->set($key, $value);
		return $formatter;
	}

	public static function get(string $key, mixed $default = '', string|null $locale = null): mixed
	{
		return Formatter::create(0, $locale)->get($key, $default);
	}

	// -----------------

	public static function number(mixed $input, int $decimals = 2, string $format = 'default', string|null $locale = null): NumberFormatter
	{
		return NumberFormatter::create($input, $locale)->decimals($decimals)->format($format);
	}

	public static function asCapacity(mixed $input, int $decimals = 0, string $format = 'default', string|null $locale = null): string
	{
		return NumberFormatter::create($input, $locale)->asCapacity($decimals, $format);
	}

}