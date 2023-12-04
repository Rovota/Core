<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum ValidationAction: int
{
	use EnumHelpers;

	case NextRule = 1;
	case NextField = 2;

}