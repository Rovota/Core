<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage\Solutions;

use Rovota\Core\Kernel\Interfaces\Solution;

class MissingDiskConfigSolution implements Solution
{

	public function getTitle(): string
	{
		return 'Try the following:';
	}

	public function getDescription(): string
	{
		return 'Ensure that you have a disk configured using the name specified. You may have made a spelling error.';
	}

	public function getDocumentationLinks(): array
	{
		return [
			'Read documentation' => 'https://rovota.gitbook.io/core/getting-started/configuration/disks'
		];
	}
	
}