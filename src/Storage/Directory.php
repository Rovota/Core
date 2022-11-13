<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage;

use Rovota\Core\Storage\Interfaces\DiskInterface;
use Rovota\Core\Storage\Traits\DirectoryFunctions;
use Rovota\Core\Support\Traits\Conditionable;

class Directory
{
	use DirectoryFunctions, Conditionable;

	public string|null $name = null;
	public string|null $path = null;
	public DiskInterface|null $disk = null;

	// -----------------

	public function __construct(array $properties)
	{
		foreach ($properties as $key => $value) {
			$this->{$key} = $value;
		}
	}

	public function __toString(): string
	{
		return $this->name;
	}

	// -----------------

	public function properties(): array
	{
		return [
			'name' => $name ?? $this->name,
			'path' => $this->path,
			'disk' => $this->disk,
		];
	}

	// -----------------

	public static function make(array $properties): Directory
	{
		return new Directory($properties);
	}

}