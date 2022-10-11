<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Support\Traits;

use Rovota\Core\Localization\LocalizationManager;
use Rovota\Core\Support\Moment;
use DateInterval;
use DateTimeZone;

trait MomentModifiers
{

	public function setTimezone(DateTimeZone|string $timezone): Moment
	{
		if (!$timezone instanceof DateTimeZone) {
			$timezone = new DateTimeZone($timezone);
		}
		parent::setTimezone($timezone);
		return $this;
	}

	public function setTimezoneUtc(): Moment
	{
		return $this->setTimezone('UTC');
	}

	public function setTimezoneLocal(): Moment
	{
		return $this->setTimezone(LocalizationManager::getActiveTimezone());
	}

	public function toUtc(): Moment
	{
		return $this->setTimezone('UTC');
	}

	public function toLocal(): Moment
	{
		return $this->setTimezone(LocalizationManager::getActiveTimezone());
	}

	// -----------------

	public function modify($modifier): Moment
	{
		parent::modify($modifier);
		return $this;
	}

	public function add(mixed ...$data): Moment
	{
		return match (true) {
			is_string($data[0]) => $this->modify($data[0]),
			$data[0] instanceof DateInterval => parent::add($data[0]),
			is_int($data[0]) && is_string($data[1]) => $this->modify(sprintf('+%d %s', $data[0], $data[1])),
			default => $this
		};
	}

	public function sub(mixed ...$data): Moment
	{
		return match (true) {
			is_string($data[0]) => $this->modify($data[0]),
			$data[0] instanceof DateInterval => parent::add($data[0]),
			is_int($data[0]) && is_string($data[1]) => $this->modify(sprintf('-%d %s', $data[0], $data[1])),
			default => $this
		};
	}

	// -----------------

	public function addSecond(): Moment
	{
		return $this->add(1, 'second');
	}

	public function addSeconds(int $seconds): Moment
	{
		return $this->add($seconds, 'seconds');
	}

	public function subSecond(): Moment
	{
		return $this->sub(1, 'second');
	}

	public function subSeconds(int $seconds): Moment
	{
		return $this->sub($seconds, 'seconds');
	}

	// -----------------

	public function addMinute(): Moment
	{
		return $this->add(1, 'minute');
	}

	public function addMinutes(int $minutes): Moment
	{
		return $this->add($minutes, 'minutes');
	}

	public function subMinute(): Moment
	{
		return $this->sub(1, 'minute');
	}

	public function subMinutes(int $minutes): Moment
	{
		return $this->sub($minutes, 'minutes');
	}

	// -----------------

	public function addHour(): Moment
	{
		return $this->add(1, 'hour');
	}

	public function addHours(int $hours): Moment
	{
		return $this->add($hours, 'hours');
	}

	public function subHour(): Moment
	{
		return $this->sub(1, 'hour');
	}

	public function subHours(int $hours): Moment
	{
		return $this->sub($hours, 'hours');
	}

	// -----------------

	public function addDay(): Moment
	{
		return $this->add(1, 'day');
	}

	public function addDays(int $days): Moment
	{
		return $this->add($days, 'days');
	}

	public function subDay(): Moment
	{
		return $this->sub(1, 'day');
	}

	public function subDays(int $days): Moment
	{
		return $this->sub($days, 'days');
	}

	// -----------------

	public function addWeekday(): Moment
	{
		return $this->add(1, 'weekday');
	}

	public function addWeekdays(int $weekdays): Moment
	{
		return $this->add($weekdays, 'weekdays');
	}

	public function subWeekday(): Moment
	{
		return $this->sub(1, 'weekday');
	}

	public function subWeekdays(int $weekdays): Moment
	{
		return $this->sub($weekdays, 'weekdays');
	}

	// -----------------

	public function addWeek(): Moment
	{
		return $this->add(1, 'week');
	}

	public function addWeeks(int $weeks): Moment
	{
		return $this->add($weeks, 'weeks');
	}

	public function subWeek(): Moment
	{
		return $this->sub(1, 'week');
	}

	public function subWeeks(int $weeks): Moment
	{
		return $this->sub($weeks, 'weeks');
	}

	// -----------------

	public function addMonthWithoutOverflow(): Moment
	{
		return $this->addMonthsWithoutOverflow(1);
	}

	public function addMonthsWithoutOverflow(int $months): Moment
	{
		$day = $this->format('j');
		$this->modify("first day of +$months months");
		$this->modify('+'.(min($day, $this->format('t')) - 1).' days');
		return $this;
	}

	public function subMonthWithoutOverflow(): Moment
	{
		return $this->subMonthsWithoutOverflow(1);
	}

	public function subMonthsWithoutOverflow(int $months): Moment
	{
		$day = $this->format('j');
		$this->modify("first day of -$months months");
		$this->modify('+'.(min($day, $this->format('t')) - 1).' days');
		return $this;
	}

	public function addMonth(): Moment
	{
		return $this->add(1, 'month');
	}

	public function addMonths(int $months): Moment
	{
		return $this->add($months, 'months');
	}

	public function subMonth(): Moment
	{
		return $this->sub(1, 'month');
	}

	public function subMonths(int $months): Moment
	{
		return $this->sub($months, 'months');
	}

	// -----------------

	public function addQuarterWithoutOverflow(): Moment
	{
		return $this->addMonthsWithoutOverflow(3);
	}

	public function addQuartersWithoutOverflow(int $quarters): Moment
	{
		return $this->addMonthsWithoutOverflow($quarters * 3);
	}

	public function subQuarterWithoutOverflow(): Moment
	{
		return $this->subMonthsWithoutOverflow(3);
	}

	public function subQuartersWithoutOverflow(int $quarters): Moment
	{
		return $this->subMonthsWithoutOverflow($quarters * 3);
	}

	public function addQuarter(): Moment
	{
		return $this->add(3, 'months');
	}

	public function addQuarters(int $quarters): Moment
	{
		return $this->add($quarters * 3, 'months');
	}

	public function subQuarter(): Moment
	{
		return $this->sub(3, 'months');
	}

	public function subQuarters(int $quarters): Moment
	{
		return $this->sub($quarters * 3, 'months');
	}

	// -----------------

	public function addYear(): Moment
	{
		return $this->add(1, 'year');
	}

	public function addYears(int $years): Moment
	{
		return $this->add($years, 'years');
	}

	public function subYear(): Moment
	{
		return $this->sub(1, 'year');
	}

	public function subYears(int $years): Moment
	{
		return $this->sub($years, 'years');
	}

	// -----------------

	public function addDecade(): Moment
	{
		return $this->add(10, 'year');
	}

	public function addDecades(int $decades): Moment
	{
		return $this->add($decades * 10, 'years');
	}

	public function subDecade(): Moment
	{
		return $this->sub(10, 'year');
	}

	public function subDecades(int $decades): Moment
	{
		return $this->sub($decades * 10, 'years');
	}

	// -----------------

	public function nextHour(): Moment
	{
		return $this->modify('next hour');
	}

	public function nextDay(): Moment
	{
		return $this->modify('next day');
	}

	public function nextWeekday(): Moment
	{
		return $this->modify('next weekday');
	}

	public function nextMonday(): Moment
	{
		return $this->modify('next Monday');
	}

	public function nextTuesday(): Moment
	{
		return $this->modify('next Tuesday');
	}

	public function nextWednesday(): Moment
	{
		return $this->modify('next Wednesday');
	}

	public function nextThursday(): Moment
	{
		return $this->modify('next Thursday');
	}

	public function nextFriday(): Moment
	{
		return $this->modify('next Friday');
	}

	public function nextSaturday(): Moment
	{
		return $this->modify('next Saturday');
	}

	public function nextSunday(): Moment
	{
		return $this->modify('next Sunday');
	}

	public function nextWeek(): Moment
	{
		return $this->modify('next week');
	}

	public function nextMonth(): Moment
	{
		return $this->modify('next month');
	}

	public function nextYear(): Moment
	{
		return $this->modify('next year');
	}

	// -----------------

	public function previousHour(): Moment
	{
		return $this->modify('previous hour');
	}

	public function previousDay(): Moment
	{
		return $this->modify('previous day');
	}

	public function previousWeekday(): Moment
	{
		return $this->modify('previous weekday');
	}

	public function previousMonday(): Moment
	{
		return $this->modify('previous Monday');
	}

	public function previousTuesday(): Moment
	{
		return $this->modify('previous Tuesday');
	}

	public function previousWednesday(): Moment
	{
		return $this->modify('previous Wednesday');
	}

	public function previousThursday(): Moment
	{
		return $this->modify('previous Thursday');
	}

	public function previousFriday(): Moment
	{
		return $this->modify('previous Friday');
	}

	public function previousSaturday(): Moment
	{
		return $this->modify('previous Saturday');
	}

	public function previousSunday(): Moment
	{
		return $this->modify('previous Sunday');
	}

	public function previousWeek(): Moment
	{
		return $this->modify('previous week');
	}

	public function previousMonth(): Moment
	{
		return $this->modify('previous month');
	}

	public function previousYear(): Moment
	{
		return $this->modify('previous year');
	}

}