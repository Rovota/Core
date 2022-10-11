<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Support\Traits;

use DateTime;

trait MomentValidation
{

	public function isNull(): bool
	{
		return $this->source === null;
	}

	public function isNotNull(): bool
	{
		return $this->source !== null;
	}

	// -----------------

	public function isWeekday(): bool
	{
		$number = (int)$this->format('w');
		return $number !== 0 && $number !== 6;
	}

	public function isWeekend(): bool
	{
		$number = (int)$this->format('w');
		return $number === 0 || $number === 6;
	}

	public function isToday(): bool
	{
		return $this->format('Y-m-d') === now()->format('Y-m-d');
	}

	public function isTomorrow(): bool
	{
		return $this->format('Y-m-d') === now()->addDay()->format('Y-m-d');
	}

	public function isYesterday(): bool
	{
		return $this->format('Y-m-d') === now()->subDay()->format('Y-m-d');
	}

	public function isNextDay(): bool
	{
		return $this->format('Y-m-d') === now()->addDay()->format('Y-m-d');
	}

	public function isLastDay(): bool
	{
		return $this->format('Y-m-d') === now()->subDay()->format('Y-m-d');
	}

	public function isNextWeek(): bool
	{
		return $this->format('Y-W') === now()->addWeek()->format('Y-W');
	}

	public function isLastWeek(): bool
	{
		return $this->format('Y-W') === now()->subWeek()->format('Y-W');
	}

	public function isNextMonth(): bool
	{
		return $this->format('Y-m') === now()->addMonthWithoutOverflow()->format('Y-m');
	}

	public function isLastMonth(): bool
	{
		return $this->format('Y-m') === now()->subMonthWithoutOverflow()->format('Y-m');
	}

	public function isMonday(): bool
	{
		return $this->getDayOfWeek() === self::MONDAY;
	}

	public function isTuesday(): bool
	{
		return $this->getDayOfWeek() === self::TUESDAY;
	}

	public function isWednesday(): bool
	{
		return $this->getDayOfWeek() === self::WEDNESDAY;
	}

	public function isThursday(): bool
	{
		return $this->getDayOfWeek() === self::THURSDAY;
	}

	public function isFriday(): bool
	{
		return $this->getDayOfWeek() === self::FRIDAY;
	}

	public function isSaturday(): bool
	{
		return $this->getDayOfWeek() === self::SATURDAY;
	}

	public function isSunday(): bool
	{
		return $this->getDayOfWeek() === self::SUNDAY;
	}

	public function isDay(array|int $day): bool
	{
		if (is_array($day)) {
			if (in_array($this->getDayOfWeek(), $day, true)) {
				return true;
			}
			return false;
		}
		return $this->getDayOfWeek() === $day;
	}

	// -----------------

	public function isNight(): bool
	{
		return $this->isTimeBetween('23:00:00', '1:59:59');
	}

	public function isMorning(): bool
	{
		return $this->isTimeBetween('2:00:00', '11:59:59');
	}

	public function isAfternoon(): bool
	{
		return $this->isTimeBetween('12:00:00', '17:00:59');
	}

	public function isEvening(): bool
	{
		return $this->isTimeBetween('17:01:00', '22:59:59');
	}

	// -----------------

	public function isFuture(): bool
	{
		return $this > now();
	}

	public function isPast(): bool
	{
		return $this < now();
	}

	// -----------------

	public function isEqual(mixed $target): bool
	{
		return $this == moment($target);
	}

	public function isAfter(mixed $target): bool
	{
		return !($this <= moment($target));
	}

	public function isAfterOrEqual(mixed $target): bool
	{
		return !($this < moment($target));
	}

	public function isBefore(mixed $target): bool
	{
		return !($this >= moment($target));
	}

	public function isBeforeOrEqual(mixed $target): bool
	{
		return !($this > moment($target));
	}

	public function isBetween(mixed $start, mixed $end): bool
	{
		return !($this < moment($start) || $this > moment($end));
	}

	public function isOutside(mixed $start, mixed $end): bool
	{
		return !($this >= moment($start) && $this <= moment($end));
	}

	// -----------------

	public function isTimeBetween(mixed $start, mixed $end): bool
	{
		$start = DateTime::createFromFormat('!H:i:s', moment($start)->format('H:i:s'));
		$end = DateTime::createFromFormat('!H:i:s', moment($end)->format('H:i:s'));
		$input = DateTime::createFromFormat('!H:i:s', $this->format('H:i:s'));
		if ($start > $end) $end->modify('+1 day');
		return ($start <= $input && $input <= $end) || ($start <= $input->modify('+1 day') && $input <= $end);
	}

	// -----------------

	public function isSameAs(string $format, mixed $target): bool
	{
		return $target->format($format) === $this->format($format);
	}

	public function isSameUnit(string $unit, mixed $target): bool
	{
		return $target->getUnit($unit) === $this->getUnit($unit);
	}

	public function isSameQuarter(mixed $target, bool $same_year = true): bool
	{
		if ($same_year&& $target->getYear() !== $this->getYear()) {
			return false;
		}
		return $target->getQuarter() === $this->getQuarter();
	}

	public function isSameMonth(mixed $target, bool $same_year = true): bool
	{
		if ($same_year && $target->getYear() !== $this->getYear()) {
			return false;
		}
		return $target->getMonth() === $this->getMonth();
	}

	public function isSameWeek(mixed $target, bool $same_year = true): bool
	{
		if ($same_year && $target->getYear() !== $this->getYear()) {
			return false;
		}
		return $target->getWeek() === $this->getWeek();
	}

	public function isSameDay(mixed $target, bool $same_year = true): bool
	{
		if ($same_year && $target->getYear() !== $this->getYear()) {
			return false;
		}
		return $target->format('z') === $this->format('z');
	}

	public function isCurrentUnit(string $unit): bool
	{
		return now()->getUnit($unit) === $this->getUnit($unit);
	}

	public function isCurrentQuarter(bool $same_year = true): bool
	{
		if ($same_year && now()->getYear() !== $this->getYear()) {
			return false;
		}
		return now()->getQuarter() === $this->getQuarter();
	}

	public function isCurrentMonth(bool $same_year = true): bool
	{
		if ($same_year && now()->getYear() !== $this->getYear()) {
			return false;
		}
		return now()->getMonth() === $this->getMonth();
	}

	public function isCurrentWeek(bool $same_year = true): bool
	{
		if ($same_year && now()->getYear() !== $this->getYear()) {
			return false;
		}
		return now()->getWeek() === $this->getWeek();
	}

	public function isDayOfWeek(int|string $day): bool
	{
		if (is_string($day)) {
			$day = match (strtolower($day)) {
				'monday' => 1,
				'tuesday' => 2,
				'wednesday' => 3,
				'thursday' => 4,
				'friday' => 5,
				'saturday' => 6,
				'sunday' => 0,
			};
		}
		return $this->getDayOfWeek() === $day;
	}

}