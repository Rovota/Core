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
use Rovota\Core\Support\Collection;
use Rovota\Core\Support\Moment;
use Throwable;

trait BucketAccessors
{

	public function all(): array
	{
		return $this->data->export();
	}

	public function collect(string|null $key = null): Collection
	{
		return new Collection($key === null ? $this->export() : $this->get($key));
	}

	// -----------------

	public function only(string|array $keys, bool $allow_null = true): array
	{
		$result = [];
		foreach (is_array($keys) ? $keys : [$keys] as $key) {
			$input = $this->get($key);
			if ($input !== null || $allow_null) {
				$result[$key] = $this->get($key);
			}
		}
		return $result;
	}

	public function except(string|array $keys): array
	{
		$result = $this->all();
		foreach (is_array($keys) ? $keys : [$keys] as $key) {
			unset($result[$key]);
		}
		return $result;
	}

	// -----------------

	public function string(string $key, string $default = ''): string
	{
		return (string)$this->get($key) ?? $default;
	}

	public function bool(string $key, bool $default = false): bool
	{
		return filter_var($this->get($key, $default), FILTER_VALIDATE_BOOLEAN);
	}

	public function int(string $key, int $default = 0): int|false
	{
		return filter_var($this->get($key, $default), FILTER_VALIDATE_INT);
	}

	public function float(string $key, float $default = 0.00): float|false
	{
		return filter_var($this->get($key, $default), FILTER_VALIDATE_FLOAT);
	}

	public function array(string $key, array $default = []): array
	{
		$value = $this->get($key);
		return is_array($value) ? $value : (strlen($value) > 0 ? explode(',', $value ?? '') : $default);
	}

	public function collection(string $key, array $default = []): Collection
	{
		return new Collection($this->array($key, $default));
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

	public function moment(string $key, DateTimeZone|string|null $timezone = null): Moment|null
	{
		try {
			return $this->has($key) ? new Moment($this->get($key), $timezone) : null;
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

}