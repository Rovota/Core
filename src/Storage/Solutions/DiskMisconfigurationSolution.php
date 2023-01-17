<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage\Solutions;

use Rovota\Core\Kernel\Interfaces\Solution;

class DiskMisconfigurationSolution implements Solution
{

	public function getTitle(): string
	{
		return 'Try the following:';
	}

	public function getDescription(): string
	{
		return 'Ensure that all required parameters are set. For example, disks using the "custom" driver need parameters to be specified.';
	}

	public function getDocumentationLinks(): array
	{
		return [
			'Read documentation' => 'https://rovota.gitbook.io/core/getting-started/configuration/disks'
		];
	}
	
}