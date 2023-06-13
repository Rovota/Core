<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum ErrorLevel: string
{
	use EnumHelpers;

	case Log = 'log';
	case Debug = 'debug';
	case Info = 'info';
	case Notice = 'notice';
	case Warning = 'warning';
	case Error = 'error';
	case Critical = 'critical';
	case Alert = 'alert';
	case Emergency = 'emergency';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			ErrorLevel::Log => 'Log',
			ErrorLevel::Debug => 'Debug',
			ErrorLevel::Info => 'Info',
			ErrorLevel::Notice => 'Notice',
			ErrorLevel::Warning => 'Warning',
			ErrorLevel::Error => 'Error',
			ErrorLevel::Critical => 'Critical',
			ErrorLevel::Alert => 'Alert',
			ErrorLevel::Emergency => 'Emergency',
		};
	}

}