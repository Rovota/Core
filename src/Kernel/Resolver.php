<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Kernel;

use BackedEnum;
use BadMethodCallException;
use Closure;
use DateTime;
use Rovota\Core\Support\Collection;
use Rovota\Core\Support\FluentString;
use Rovota\Core\Support\Moment;

final class Resolver
{

	protected function __construct()
	{
	}

	// -----------------

	public static function invoke(mixed $target, array $parameters = []): mixed
	{
		if ($target instanceof Closure) {
			return call_user_func_array($target, $parameters);
		}

		if (is_array($target)) {
			[$controller, $method] = $target;
			$controller = new $controller();

			if (is_callable([$controller, $method])) {
				return call_user_func_array([$controller, $method], $parameters);
			} else {
				throw new BadMethodCallException("The method specified cannot be called: $controller@$method.");
			}
		}

		return null;
	}

	public static function getValueType(mixed $value): string
	{
		return match(true) {
			$value instanceof BackedEnum => 'enum',
			$value instanceof Collection => 'collection',
			$value instanceof Moment => 'moment',
			$value instanceof DateTime => 'datetime',
			$value instanceof FluentString => 'fluent_string',
			is_array($value) => 'array',
			is_bool($value) => 'bool',
			is_float($value) => 'float',
			is_int($value) => 'int',
			is_string($value) => 'string',
			default => gettype($value),
		};
	}

}