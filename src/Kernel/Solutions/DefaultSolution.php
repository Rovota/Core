<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Kernel\Solutions;

use Rovota\Core\Kernel\Interfaces\Solution;

class DefaultSolution implements Solution
{

	public function getTitle(): string
	{
		return 'Solution';
	}

	public function getDescription(): string
	{
		return 'Check whether the spelling is correct, and make sure that all classes can be found.';
	}

	public function getDocumentationLinks(): array
	{
		return [];
	}

}