<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Kernel\MiddlewareManager;

final class Middleware
{

	protected function __construct()
	{
	}

	// -----------------

	public static function register(string $name, string $class, bool $global = false): void
	{
		MiddlewareManager::register($name, $class, $global);
	}

	public static function global(array|string $names): void
	{
		MiddlewareManager::global($names);
	}

}