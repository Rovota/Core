<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Kernel\Solutions;

use Rovota\Core\Kernel\Interfaces\Solution;

class SystemRequirementSolution implements Solution
{

	public function getTitle(): string
	{
		return 'Incompatibility';
	}

	public function getDescription(): string
	{
		return 'You need to make sure your PHP version and extensions are compatible.';
	}

	public function getDocumentationLinks(): array
	{
		return ['System Requirements' => 'https://docs.rovota.com'];
	}
}