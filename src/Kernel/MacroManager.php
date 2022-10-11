<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Kernel;

use Closure;

/**
 * @internal
 */
final class MacroManager
{

	protected static array $macros = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function register(string $class, string $name, Closure $macro): void
	{
		self::$macros[$class][$name] = $macro;
	}

	public static function forget(string $class, string $name): void
	{
		unset(self::$macros[$class][$name]);
	}

	public static function flush(string $class): void
	{
		unset(self::$macros[$class]);
	}

	public static function has(string $class, string $name): bool
	{
		return isset(self::$macros[$class][$name]);
	}

	public static function get(string $class, string $name): Closure|null
	{
		return self::$macros[$class][$name] ?? null;
	}

}