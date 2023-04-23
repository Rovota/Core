<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Routing\UrlBuilder;

final class Url
{

	protected function __construct()
	{
	}

	// -----------------

	public static function make(): UrlBuilder
	{
		return new UrlBuilder();
	}

	// -----------------

	public static function to(string $path, array $query = []): UrlBuilder
	{
		return self::make()->local($path, $query);
	}

	public static function toRoute(string $name, array $params = [], array $query = []): UrlBuilder
	{
		return self::make()->route($name, $params, $query);
	}

	public static function toSubdomain(string $name, string $path = '/', array $query = []): UrlBuilder
	{
		return self::make()->subdomain($name)->path($path)->query($query);
	}

	public static function toPrevious(string $default = '/', array $query = []): UrlBuilder
	{
		return self::make()->previous($default, $query);
	}

	public static function toNext(string $default = '/', array $query = []): UrlBuilder
	{
		return self::make()->next($default, $query);
	}

	public static function toIntended(string $default = '/', array $query = []): UrlBuilder
	{
		return self::make()->intended($default, $query);
	}

	public static function toForeign(string $location, array $query = []): UrlBuilder
	{
		return self::make()->foreign($location, $query);
	}


}