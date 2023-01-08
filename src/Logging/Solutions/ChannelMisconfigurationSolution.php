<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Logging\Solutions;

use Rovota\Core\Kernel\Interfaces\Solution;

class ChannelMisconfigurationSolution implements Solution
{

	public function getTitle(): string
	{
		return 'Try the following:';
	}

	public function getDescription(): string
	{
		return 'Ensure that all required parameters are set. For example, channels using the "monolog" driver need parameters to be specified.';
	}

	public function getDocumentationLinks(): array
	{
		return [
			'Read documentation' => 'https://rovota.gitbook.io/core/getting-started/configuration/logging'
		];
	}
	
}