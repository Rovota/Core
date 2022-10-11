<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Database\Casts;

use Rovota\Core\Facades\Crypt;
use Rovota\Core\Kernel\ExceptionHandler;
use Throwable;

final class EncryptionCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return true;
	}

	// -----------------

	public function get(mixed $value, array $options): mixed
	{
		try {
			return Crypt::decrypt($value);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable, true);
			return null;
		}
	}

	public function set(mixed $value, array $options): string|null
	{
		try {
			return Crypt::encrypt($value);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable, true);
			return null;
		}
	}

}