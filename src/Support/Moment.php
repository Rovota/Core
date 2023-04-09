<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support;

use JsonSerializable;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Localization\LocalizationManager;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\Traits\Macroable;
use Rovota\Core\Support\Traits\MomentFormatters;
use Rovota\Core\Support\Traits\MomentModifiers;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Rovota\Core\Support\Traits\MomentValidation;
use Throwable;

final class Moment extends DateTime implements JsonSerializable
{
	use Macroable, Conditionable, MomentModifiers, MomentFormatters, MomentValidation;

	// -----------------

	public const MONDAY = 1;
	public const TUESDAY = 2;
	public const WEDNESDAY = 3;
	public const THURSDAY = 4;
	public const FRIDAY = 5;
	public const SATURDAY = 6;
	public const SUNDAY = 0;

	protected bool $unavailable = false;

	protected mixed $source = null;

	// -----------------

	public function __construct(mixed $datetime = 'now', DateTimeZone|string|null $timezone = null)
	{
		$this->source = $datetime;

		$datetime = match (true) {
			$datetime instanceof DateTime => $datetime->format('Y-m-d H:i:s'),
			is_int($datetime) => date('Y-m-d H:i:s', $datetime),
			is_numeric($datetime) => date('Y-m-d H:i:s', (int)$datetime),
			is_string($datetime) => $datetime,
			default => null
		};
		if ($datetime === null) {
			$this->unavailable = true;
		}
		if ($datetime instanceof DateTime && is_string($timezone) === false) {
			$timezone = $datetime->getTimezone();
		}
		if (is_string($timezone)) {
			$timezone = ($timezone === 'local') ? LocalizationManager::getActiveTimezone() : new DateTimeZone($timezone);
		}
		parent::__construct($datetime, $timezone);
	}

	public function __toString(): string
	{
		return $this->format($this->default_format);
	}

	// -----------------

	public static function create(mixed $datetime = 'now', DateTimeZone|null $timeZone = null): Moment|null
	{
		try {
			return new Moment($datetime, $timeZone);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}

	public static function createFromFormat(string $format, string $datetime, DateTimeZone|null $timezone = null): Moment|false
	{
		try {
			return new Moment(parent::createFromFormat($format, $datetime, $timezone), $timezone);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return false;
	}

	public static function now(): Moment|null
	{
		try {
			return new Moment();
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}

	public static function createLocal(mixed $datetime = 'now'): Moment|null
	{
		try {
			return new Moment($datetime, 'local');
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}

	public function clone(): Moment|null
	{
		try {
			return new Moment($this, $this->getTimezone());
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}

	// -----------------

	public function difference(DateTimeInterface|string|int $target = 'now', bool $absolute = true): MomentInterval|null
	{
		try {
			if (!$target instanceof DateTimeInterface) {
				$target = new Moment($target);
			}
			return new MomentInterval(parent::diff($target, $absolute));
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}

	public function diffInDecades(DateTimeInterface|string|int $target = 'now', bool $absolute = true): int
	{
		return $this->difference($target, $absolute)->inDecades();
	}

	public function diffInYears(DateTimeInterface|string|int $target = 'now', bool $absolute = true): int
	{
		return $this->difference($target, $absolute)->inYears();
	}

	public function diffInQuarters(DateTimeInterface|string|int $target = 'now', bool $absolute = true): int
	{
		return $this->difference($target, $absolute)->inQuarters();
	}

	public function diffInMonths(DateTimeInterface|string|int $target = 'now', bool $absolute = true): int
	{
		return $this->difference($target, $absolute)->inMonths();
	}

	public function diffInDays(DateTimeInterface|string|int $target = 'now', bool $absolute = true): int
	{
		return $this->difference($target, $absolute)->inDays();
	}

	public function diffInWeeks(DateTimeInterface|string|int $target = 'now', bool $absolute = true): int
	{
		return $this->difference($target, $absolute)->inWeeks();
	}

	public function diffInHours(DateTimeInterface|string|int $target = 'now', bool $absolute = true): int
	{
		return $this->difference($target, $absolute)->inHours();
	}

	public function diffInMinutes(DateTimeInterface|string|int $target = 'now', bool $absolute = true): int
	{
		return $this->difference($target, $absolute)->inMinutes();
	}

	public function diffInSeconds(DateTimeInterface|string|int $target = 'now', bool $absolute = true): int
	{
		return $this->difference($target, $absolute)->inSeconds();
	}

	public function diffInMicroseconds(DateTimeInterface|string|int $target = 'now', bool $absolute = true): int
	{
		return $this->difference($target, $absolute)->inMicroseconds();
	}

	// -----------------

	public function getTimeOfDayPeriod(): int
	{
		return match (true) {
			$this->isNight() => 0,
			$this->isMorning() => 1,
			$this->isAfternoon() => 2,
			$this->isEvening() => 3,
		};
	}

	// -----------------

	public function getUnit(string $unit): int
	{
		return match ($unit) {
			'second' => $this->getSecond(),
			'minute' => $this->getMinute(),
			'hour' => $this->getHour(),
			'day' => $this->getDay(),
			'week' => $this->getWeek(),
			'month' => $this->getMonth(),
			'quarter' => $this->getQuarter(),
			'year' => $this->getYear(),
			default => 0
		};
	}

	public function getSecond(): int
	{
		return (int)ltrim($this->format('s'), '0');
	}

	public function getMinute(): int
	{
		return (int)ltrim($this->format('i'), '0');
	}

	public function getHour(): int
	{
		return (int)$this->format('G');
	}

	public function getDay(): int
	{
		return (int)$this->format('j');
	}

	public function getDayOfWeek(): int
	{
		return (int)$this->format('w');
	}

	public function getWeek(): int
	{
		return (int)ltrim($this->format('W'), '0');
	}

	public function getMonth(): int
	{
		return (int)$this->format('n');
	}

	public function getQuarter(): int
	{
		return match ($this->getMonth()) {
			1, 2, 3 => 1,
			4, 5, 6 => 2,
			7, 8, 9 => 3,
			10, 11, 12 => 4,
		};
	}

	public function getYear(): int
	{
		return (int)$this->format('Y');
	}

}