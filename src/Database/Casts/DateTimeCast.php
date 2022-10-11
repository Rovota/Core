<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Database\Casts;

use DateTime;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Support\Moment;
use Throwable;

final class DateTimeCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return $value instanceof DateTime && $value instanceof Moment === false;
	}

	public function supportsValue(mixed $value): bool
	{
		return $value instanceof DateTime && $value instanceof Moment === false;
	}

	// -----------------

	public function get(mixed $value, array $options): DateTime|null
	{
		try {
			return new DateTime($value);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
			return null;
		}
	}

	public function set(mixed $value, array $options): string
	{
		return $value->format('Y-m-d H:i:s');
	}

}