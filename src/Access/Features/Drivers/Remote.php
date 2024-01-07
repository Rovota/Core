<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 *
 * Inspired by Laravel rate limiting.
 */

namespace Rovota\Core\Access\Features\Drivers;

use Rovota\Core\Access\Features\Feature;

final class Remote extends Feature
{

	protected function resolve(): string|false
	{
		$enabled = $this->config->bool('enabled');
		$variant = $this->config->get('variant');

		return $enabled === true ? ($variant ?? true) : false;
	}

}