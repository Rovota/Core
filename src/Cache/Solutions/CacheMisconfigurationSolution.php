<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Solutions;

use Rovota\Core\Kernel\Interfaces\Solution;

class CacheMisconfigurationSolution implements Solution
{

	public function getTitle(): string
	{
		return 'Try the following:';
	}

	public function getDescription(): string
	{
		return 'Ensure that all required parameters are set. For example, caches using the "redis" driver need connection parameters to be specified.';
	}

	public function getDocumentationLinks(): array
	{
		return [
			'Read documentation' => 'https://rovota.gitbook.io/core/getting-started/configuration/caches'
		];
	}
	
}