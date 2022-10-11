<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Database\Casts;

use Rovota\Core\Facades\Crypt;
use Rovota\Core\Kernel\ExceptionHandler;
use Stringable;
use Throwable;

final class EncryptionStringCast extends Cast
{

	public function allowedValue(mixed $value, array $options): bool
	{
		return $value instanceof Stringable || is_scalar($value);
	}

	// -----------------

	public function get(mixed $value, array $options): string|null
	{
		try {
			return Crypt::decryptString($value);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable, true);
			return null;
		}
	}

	public function set(mixed $value, array $options): string|null
	{
		try {
			return Crypt::encryptString((string)$value);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable, true);
			return null;
		}
	}

}