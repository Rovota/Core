<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Logging\Solutions;

use Rovota\Core\Kernel\Interfaces\Solution;

class UnsupportedDriverSolution implements Solution
{

	public function getTitle(): string
	{
		return 'Supported Drivers';
	}

	public function getDescription(): string
	{
		return 'Currently, only the stack, stream, discord and monolog drivers are supported.';
	}

	public function getDocumentationLinks(): array
	{
		return ['Read more about this' => ''];
	}

}