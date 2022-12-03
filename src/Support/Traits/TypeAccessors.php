<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support\Traits;

use BackedEnum;
use DateTime;
use DateTimeZone;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Text;
use Throwable;

trait TypeAccessors
{

	public function array(string $key, array $default = []): array
	{
		$value = $this->get($key);
		return is_array($value) ? $value : (strlen($value) > 0 ? explode(',', $value ?? '') : $default);
	}

	public function bool(string $key, bool $default = false): bool
	{
		return filter_var($this->get($key, $default), FILTER_VALIDATE_BOOLEAN);
	}

	public function date(string $key, DateTimeZone|null $timezone = null): DateTime|null
	{
		try {
			return $this->has($key) ? new DateTime($this->string($key), $timezone) : null;
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}

	public function enum(string $key, BackedEnum|string $class, BackedEnum|null $default = null): BackedEnum|null
	{
		$value = $this->get($key);
		return ($value !== null) ? $class::TryFrom($value) : $default;
	}

	public function float(string $key, float $default = 0.00): float|false
	{
		return filter_var($this->get($key, $default), FILTER_VALIDATE_FLOAT);
	}

	public function int(string $key, int $default = 0): int|false
	{
		return filter_var($this->get($key, $default), FILTER_VALIDATE_INT);
	}

	public function moment(string $key, DateTimeZone|string|null $timezone = null): Moment|null
	{
		try {
			return $this->has($key) ? new Moment($this->get($key), $timezone) : null;
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}

	public function string(string $key, string $default = ''): string
	{
		return (string)$this->get($key, $default);
	}

	public function text(string $key, Text|string $default = ''): Text
	{
		return new Text($this->get($key, $default));
	}

}