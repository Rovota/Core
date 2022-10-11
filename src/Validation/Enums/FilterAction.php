<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Validation\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum FilterAction: string
{
	use EnumHelpers;

	case Allow = 'allow';
	case Block = 'block';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			FilterAction::Allow => 'Allow',
			FilterAction::Block => 'Block',
		};
	}

}