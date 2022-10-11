<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Addon;

use Rovota\Core\Facades\Localization;
use stdClass;

class Library extends Addon
{
	/**
	 * Creates new instance while trying to load translations automatically.
	 */
	public static function newFromBuilder(stdClass $class): static
	{
		$instance = parent::newFromBuilder($class);
		Localization::addToSource('core', 'libraries/'.$instance->name);

		return $instance;
	}

}