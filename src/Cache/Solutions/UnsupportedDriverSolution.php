<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Cache\Solutions;

use Rovota\Core\Kernel\Interfaces\Solution;

class UnsupportedDriverSolution implements Solution
{

	public function getTitle(): string
	{
		return 'Supported Drivers';
	}

	public function getDescription(): string
	{
		return 'Make sure you have the latest version installed, and that the driver name is spelled correctly.';
	}

	public function getDocumentationLinks(): array
	{
		return [
			'Read documentation' => 'https://rovota.gitbook.io/core/getting-started/configuration/caches'
		];
	}
	
}