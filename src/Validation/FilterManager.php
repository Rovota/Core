<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation;

use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Support\Enums\Status;
use Throwable;

final class FilterManager
{
	/**
	 * @var array<string, Filter>
	 */
	protected static array $filters = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		try {
			self::$filters = Filter::where('status', Status::Enabled)->getBy('name')->toArray();
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable, true);
		}
	}

	// -----------------

	public static function has(string $name): bool
	{
		return isset(self::$filters[$name]);
	}

	public static function get(string $name): Filter|null
	{
		return self::$filters[$name] ?? null;
	}

	/**
	 * @returns array<string, Filter>
	 */
	public static function all(): array
	{
		return self::$filters;
	}

}