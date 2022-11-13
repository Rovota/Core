<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Convert\ConversionManager;

final class Convert
{

	protected function __construct()
	{
	}

	// -----------------

	public static function toHtml(string $string, string|null $language = null): string
	{
		return ConversionManager::toHtml($string, $language);
	}

	public static function toAscii(string $string): string
	{
		return ConversionManager::toAscii($string);
	}

	// -----------------

	public static function textToBytes(string $size): int
	{
		return ConversionManager::textToBytes($size);
	}

}