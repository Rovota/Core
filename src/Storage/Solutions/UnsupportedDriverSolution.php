<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Storage\Solutions;

use Rovota\Core\Kernel\Interfaces\Solution;

class UnsupportedDriverSolution implements Solution
{

	public function getTitle(): string
	{
		return 'Supported Drivers';
	}

	public function getDescription(): string
	{
		return 'Currently, only the local, s3, sftp and flysystem drivers are supported.';
	}

	public function getDocumentationLinks(): array
	{
		return ['Read more about this' => ''];
	}
	
}