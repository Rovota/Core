<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Units;

use Rovota\Core\Convert\Enums\UnitType;
use Rovota\Core\Convert\Units\Time\Day;
use Rovota\Core\Convert\Units\Time\Hour;
use Rovota\Core\Convert\Units\Time\MilliSecond;
use Rovota\Core\Convert\Units\Time\Minute;
use Rovota\Core\Convert\Units\Time\Second;
use Rovota\Core\Convert\Units\Time\Week;
use Rovota\Core\Convert\Units\Time\Year;

abstract class Time extends Unit
{
	const UNIT_TYPE = UnitType::Time;

	const BASE_UNIT = Second::class;

	// -----------------

	public static function classFromIdentifier(string $identifier): string|null
	{
		return match($identifier) {
			'y', 'year' => Year::class,
			'w', 'week' => Week::class,
			'd', 'day' => Day::class,
			'h', 'hour' => Hour::class,
			'm', 'minute' => Minute::class,
			's', 'second' => Second::class,
			'ms', 'millisecond' => MilliSecond::class,
			default => null,
		};
	}

}