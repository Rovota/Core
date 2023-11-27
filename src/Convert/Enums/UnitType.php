<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Enums;

use Rovota\Core\Convert\Units\Length;
use Rovota\Core\Convert\Units\Temperature;
use Rovota\Core\Support\Traits\EnumHelpers;

enum UnitType: int
{
	use EnumHelpers;

//	case Mass = 1;
	case Length = 2;
//	case Area = 3;
//	case Volume = 4;
//	case Time = 5;
	case Temperature = 6;
//	case Amount = 7;
//	case Velocity = 8;

	// -----------------

	public function label(): string
	{
		return match ($this) {
//			UnitType::Mass => 'Mass',
			UnitType::Length => 'Length',
//			UnitType::Area => 'Area',
//			UnitType::Volume => 'Volume',
//			UnitType::Time => 'Time',
			UnitType::Temperature => 'Temperature',
//			UnitType::Amount => 'Amount',
//			UnitType::Velocity => 'Velocity',
		};
	}

	public function class(): string
	{
		return match ($this) {
//			UnitType::Mass => '',
			UnitType::Length => Length::class,
//			UnitType::Area => '',
//			UnitType::Volume => '',
//			UnitType::Time => '',
			UnitType::Temperature => Temperature::class,
//			UnitType::Amount => '',
//			UnitType::Velocity => '',
		};
	}

}