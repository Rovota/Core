<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Closure;
use Rovota\Core\Http\Throttling\Limit;
use Rovota\Core\Http\Throttling\Limiter;
use Rovota\Core\Http\Throttling\LimitManager;

final class RateLimiting
{

	protected function __construct()
	{
	}

	// -----------------

	public static function limiter(string $name): Limiter
	{
		return LimitManager::get($name);
	}

	// -----------------

	public static function register(string $name, Closure|Limit $callback): Limiter
	{
		return LimitManager::register($name, $callback);
	}

	// -----------------

	public static function current(): Limiter|null
	{
		return LimitManager::activeLimiter();
	}

	public static function get(string $name): Limiter|null
	{
		return LimitManager::get($name);
	}

	/**
	 * @returns array<string, Limiter>
	 */
	public static function all(): array
	{
		return LimitManager::all();
	}

	// -----------------

	public static function limit(string $name): Limit|null
	{
		return LimitManager::activeLimiter()->limit($name);
	}

	/**
	 * @returns array<string, Limit>
	 */
	public static function limits(): array
	{
		return LimitManager::activeLimiter()->limits();
	}

	// -----------------

	public static function hit(): void
	{
		LimitManager::activeLimiter()->hit();
	}

	public static function attempts(): int
	{
		return LimitManager::activeLimiter()->attempts();
	}

	public static function tooManyAttempts(): bool
	{
		return LimitManager::activeLimiter()->tooManyAttempts();
	}

	public static function reset(): void
	{
		LimitManager::activeLimiter()->reset();
	}

	/**
	 * @returns array<string, int>
	 */
	public static function remaining(): array
	{
		return LimitManager::activeLimiter()->remaining();
	}

}