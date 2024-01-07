<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Access\Features;

use Rovota\Core\Access\Features\Enums\Driver;
use Rovota\Core\Support\Config;

/**
 * @property Driver|null $driver
 * @property string $label
 * @property string $description
 */
final class FeatureConfig extends Config
{

	protected function driver(): Driver|null
	{
		return Driver::tryFrom($this->get('driver', '-'));
	}

	// -----------------

	protected function label(): string
	{
		return $this->get('label', 'Unnamed Feature');
	}

	protected function description(): string
	{
		return $this->get('description', 'No description available.');
	}

}