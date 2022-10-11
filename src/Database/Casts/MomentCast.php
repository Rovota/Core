<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Database\Casts;

use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Support\Moment;
use Throwable;

final class MomentCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return $value instanceof Moment;
	}

	public function supportsValue(mixed $value): bool
	{
		return $value instanceof Moment;
	}

	// -----------------

	public function get(mixed $value, array $options): Moment|null
	{
		try {
			return new Moment($value);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
			return null;
		}
	}

	public function set(mixed $value, array $options): string
	{
		return $value->toUtcDateTimeString();
	}

}