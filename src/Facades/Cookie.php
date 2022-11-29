<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Cookie\Cookie as CookieModel;
use Rovota\Core\Cookie\CookieManager;

final class Cookie
{

	protected function __construct()
	{
	}

	// -----------------

	public static function make(string $name, string|null $value, array $options = []): CookieModel
	{
		return CookieManager::make($name, $value, $options);
	}

	public static function forever(string $name, string|null $value, array $options = []): CookieModel
	{
		return CookieManager::forever($name, $value, $options);
	}

	// -----------------

	public static function queue(Cookie|string $name, string|null $value = null, array $options = []): void
	{
		CookieManager::queue($name, $value, $options);
	}

	public static function isQueued(string $name): bool
	{
		return CookieManager::isQueued($name);
	}

	public static function findQueued(string $name): CookieModel|null
	{
		return CookieManager::findQueued($name);
	}

	public static function removeQueued(string $name): void
	{
		CookieManager::removeQueued($name);
	}

	// -----------------

	public static function recycle(string $name, string|null $value = null, array $options = []): void
	{
		CookieManager::recycle($name, $value, $options);
	}

	public static function expire(string $name): void
	{
		CookieManager::expire($name);
	}

	public static function isReceived(string $name): bool
	{
		return CookieManager::isReceived($name);
	}

	public static function findReceived(string $name): CookieModel|null
	{
		return CookieManager::findReceived($name);
	}

}