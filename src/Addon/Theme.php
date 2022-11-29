<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Addon;

use Rovota\Core\Facades\Localization;
use stdClass;

class Theme extends Addon
{
	/**
	 * Creates new instance while trying to load translations automatically.
	 */
	public static function newFromBuilder(stdClass $class): static
	{
		$instance = parent::newFromBuilder($class);
		Localization::addToSource('core', 'themes/'.$instance->name);

		return $instance;
	}

}