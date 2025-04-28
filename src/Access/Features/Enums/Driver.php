<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Access\Features\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum Driver: string
{
	use EnumHelpers;

	case Local = 'local';
	case Remote = 'remote';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Driver::Local => 'Filesystem',
			Driver::Remote => 'Remote',
		};
	}

}