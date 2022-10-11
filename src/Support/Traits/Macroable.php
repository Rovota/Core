<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Support\Traits;

use BadMethodCallException;
use Closure;
use Rovota\Core\Kernel\MacroManager;

trait Macroable
{

	public static function macro(string $name, Closure $macro): void
	{
		MacroManager::register(static::class, $name, $macro);
	}

	public static function hasMacro(string $name): bool
	{
		return MacroManager::has(static::class, $name);
	}

	public static function deleteMacro(string $name): void
	{
		MacroManager::forget(static::class, $name);
	}

	public static function flushMacros(): void
	{
		MacroManager::flush(static::class);
	}

	// -----------------

	public static function __callStatic(string $name, array $parameters = []): mixed
	{
		if (!MacroManager::has(static::class, $name)) {
			throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $name));
		} else {
			$macro = MacroManager::get(static::class, $name);
			if ($macro instanceof Closure) {
				$macro = $macro->bindTo(null, static::class);
				return $macro(...$parameters);
			}
			return null;
		}
	}

	public function __call(string $name, array $parameters = []): mixed
	{
		if (!MacroManager::has(static::class, $name)) {
			throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $name));
		} else {
			$macro = MacroManager::get(static::class, $name);
			if ($macro instanceof Closure) {
				$macro = $macro->bindTo($this, static::class);
				return $macro(...$parameters);
			}
			return null;
		}
	}

}