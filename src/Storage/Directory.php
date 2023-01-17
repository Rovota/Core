<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage;

use Rovota\Core\Storage\Interfaces\DirectoryInterface;
use Rovota\Core\Storage\Traits\DirectoryFunctions;
use Rovota\Core\Support\Str;
use Rovota\Core\Support\Traits\Conditionable;

class Directory implements DirectoryInterface
{
	use DirectoryFunctions, Conditionable;

	protected DirectoryProperties $properties;

	// -----------------

	public function __construct(array $properties)
	{
		$this->properties = new DirectoryProperties();

		foreach ($properties as $key => $value) {
			$this->properties->set($key, $value);
		}
	}

	public function __toString(): string
	{
		return $this->properties->name;
	}

	// -----------------

	public static function make(array $properties): DirectoryInterface
	{
		return new static($properties);
	}

	// -----------------

	public function properties(): DirectoryProperties
	{
		return $this->properties;
	}

	public function publicUrl(): string
	{
		$base_path = $this->properties->disk->baseUrl().$this->properties->path;

		return Str::finish($base_path, '/').$this->properties->name;
	}

}