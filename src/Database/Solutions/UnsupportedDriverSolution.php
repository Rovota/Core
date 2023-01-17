<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Solutions;

use Rovota\Core\Kernel\Interfaces\Solution;

class UnsupportedDriverSolution implements Solution
{

	public function getTitle(): string
	{
		return 'Try the following:';
	}

	public function getDescription(): string
	{
		return 'Ensure you have the latest version of Core installed, all dependencies for this driver are present and that the driver name is spelled correctly.';
	}

	public function getDocumentationLinks(): array
	{
		return [
			'Read documentation' => 'https://rovota.gitbook.io/core/getting-started/configuration/databases'
		];
	}
	
}