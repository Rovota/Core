<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Convert\Enums;

use Rovota\Core\Convert\Units\Frequency;
use Rovota\Core\Convert\Units\FuelEconomy;
use Rovota\Core\Convert\Units\Length;
use Rovota\Core\Convert\Units\Mass;
use Rovota\Core\Convert\Units\Pressure;
use Rovota\Core\Convert\Units\Speed;
use Rovota\Core\Convert\Units\Temperature;
use Rovota\Core\Convert\Units\Time;
use Rovota\Core\Support\Traits\EnumHelpers;

enum UnitType: int
{
	use EnumHelpers;

//	case Area = 1;
//	case DataTransferRate = 2;
//	case DigitalStorage = 3;
//	case Energy = 4;
	case Frequency = 5;
	case FuelEconomy = 6;
	case Length = 7;
	case Mass = 8;
	case Pressure = 9;
	case Speed = 10;
	case Temperature = 11;
	case Time = 12;
//	case Volume = 13;

	// -----------------

	public function class(): string
	{
		return match ($this) {
//			UnitType::Area => '',
//			UnitType::DataTransferRate => '',
//			UnitType::DigitalStorage => '',
//			UnitType::Energy => '',
			UnitType::Frequency => Frequency::class,
			UnitType::FuelEconomy => FuelEconomy::class,
			UnitType::Length => Length::class,
			UnitType::Mass => Mass::class,
			UnitType::Pressure => Pressure::class,
			UnitType::Speed => Speed::class,
			UnitType::Temperature => Temperature::class,
			UnitType::Time => Time::class,
//			UnitType::Volume => '',
		};
	}

}