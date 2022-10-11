<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Support;

use DateInterval;

final class MomentInterval
{

	public float $microseconds = 0;
	public int $seconds = 0;
	public int $minutes = 0;
	public int $hours = 0;
	public int $days = 0;
	public int $months = 0;
	public int $years = 0;

	protected DateInterval $interval;

	// -----------------

	/**
	 * @throws \Exception
	 */
	public function __construct(DateInterval|string $duration)
	{
		if (is_string($duration)) {
			$duration = new DateInterval($duration);
		}

		$this->interval = $duration;

		$this->microseconds = $duration->f * 1000000;
		$this->seconds = $duration->s;
		$this->minutes = $duration->i;
		$this->hours = $duration->h;

		$this->days = $duration->d;
		$this->months = $duration->m;
		$this->years = $duration->y;
	}

	public function __toString(): string
	{
		return $this->toIsoString();
	}

	// -----------------

	public function toDateInterval(): DateInterval
	{
		return $this->interval;
	}

	public function toIsoString(): string
	{
		$string = 'P';

		$interval = $this->interval;

		if ($interval->y > 0) {
			$string .= $interval->y.'Y';
		}
		if ($interval->m > 0) {
			$string .= $interval->m.'M';
		}
		if ($interval->d > 0) {
			$string .= $interval->d.'D';
		}

		if ($interval->h > 0 || $interval->i > 0 || $interval->s > 0) {
			$string .= 'T';
			if ($interval->h > 0) {
				$string .= $interval->h.'H';
			}
			if ($interval->i > 0) {
				$string .= $interval->i.'M';
			}
			if ($interval->s > 0) {
				$string .= $interval->s.'S';
			}
		}

		return $string;
	}

	// -----------------

	public function isPast(): bool
	{
		return $this->interval->invert === 1;
	}

	// -----------------gmp_neg

	public function inDecades(): int
	{
		$result = floor($this->years / 10);
		return $this->isPast() ? -$result : $result;
	}

	public function inYears(): int
	{
		$result = $this->years;
		return $this->isPast() ? -$result : $result;
	}

	public function inQuarters(): int
	{
		$result = floor((($this->years * 12) + $this->months) / 3);
		return $this->isPast() ? -$result : $result;
	}

	public function inMonths(): int
	{
		$result = ($this->years * 12) + $this->months;
		return $this->isPast() ? -$result : $result;
	}

	public function inWeeks(): int
	{
		$result = floor($this->interval->days / 7);
		return $this->isPast() ? -$result : $result;
	}

	public function inDays(): int
	{
		$result = $this->interval->days;
		return $this->isPast() ? -$result : $result;
	}

	public function inHours(): int
	{
		$result = $this->getTotalHours();
		return $this->isPast() ? -$result : $result;
	}

	public function inMinutes(): int
	{
		$result = $this->getTotalMinutes();
		return $this->isPast() ? -$result : $result;
	}

	public function inSeconds(): int
	{
		$result = $this->getTotalSeconds();
		return $this->isPast() ? -$result : $result;
	}

	public function inMicroseconds(): int
	{
		$result = $this->getTotalMicroseconds();
		return $this->isPast() ? -$result : $result;
	}

	// -----------------

	private function getTotalHours(): int
	{
		return ($this->interval->days * 24) + $this->hours;
	}

	private function getTotalMinutes(): int
	{
		return ($this->getTotalHours() * 60) + $this->minutes;
	}

	private function getTotalSeconds(): int
	{
		return ($this->getTotalMinutes() * 60) + $this->seconds;
	}

	private function getTotalMicroseconds(): int
	{
		return ($this->getTotalSeconds() * 1000000) + $this->microseconds;
	}

}