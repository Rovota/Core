<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage;

use Rovota\Core\Storage\Interfaces\DiskInterface;
use Rovota\Core\Support\Config;

/**
 * @property string|null $name
 * @property string|null $path
 * @property DiskInterface|null $disk
 */
final class DirectoryProperties extends Config
{

	protected function name(): string|null
	{
		return $this->get('name');
	}

	protected function path(): string|null
	{
		return $this->get('path');
	}

	protected function disk(): DiskInterface|null
	{
		return $this->get('disk');
	}

}