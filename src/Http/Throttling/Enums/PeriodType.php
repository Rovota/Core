<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Http\Throttling\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum PeriodType: string
{
	use EnumHelpers;

	case Second = 'second';
	case Minute = 'minute';
	case Hour = 'hour';
	case Day = 'day';
	case Week = 'week';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			PeriodType::Second => 'Per second',
			PeriodType::Minute => 'Per minute',
			PeriodType::Hour => 'Per hour',
			PeriodType::Day => 'Per day',
			PeriodType::Week => 'Per week',
		};
	}

	public function formatted(int $limit): string
	{
		return match ($this) {
			PeriodType::Second => sprintf('%d/second', $limit),
			PeriodType::Minute => sprintf('%d/minute', $limit),
			PeriodType::Hour => sprintf('%d/hour', $limit),
			PeriodType::Day => sprintf('%d/day', $limit),
			PeriodType::Week => sprintf('%d/week', $limit),
		};
	}

}