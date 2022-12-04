<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage;

use Rovota\Core\Storage\Interfaces\FileInterface;
use Rovota\Core\Storage\Traits\FileFunctions;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Str;
use Rovota\Core\Support\Traits\Conditionable;

class File implements FileInterface
{
	use FileFunctions, Conditionable;

	protected mixed $contents = null;

	protected FileProperties $properties;

	protected bool $unsaved_changes = false;

	// -----------------

	public function __construct(mixed $contents, array $properties)
	{
		$this->contents = $contents;

		$this->properties = new FileProperties();

		foreach ($properties as $key => $value) {
			if ($key === 'last_modified') {
				$this->properties->set($key, $value instanceof Moment ? $value : moment($value));
				continue;
			}
			if ($key === 'name') {
				$this->properties->set('name', Str::beforeLast($value, '.'));
				$this->properties->set('extension', Str::afterLast($value, '.'));
				continue;
			}
			$this->properties->set($key, $value);
		}
	}

	public function __toString(): string
	{
		return $this->contents;
	}

	// -----------------

	public static function make(mixed $contents, array $properties): FileInterface
	{
		return new static($contents, $properties);
	}

	// -----------------

	public function contents(): mixed
	{
		return $this->contents;
	}

	public function properties(): FileProperties
	{
		return $this->properties;
	}

	public function publicUrl(): string
	{
		$base_path = $this->properties->disk->baseUrl().$this->properties->path;
		$file_name = sprintf('%s.%s', $this->properties->name, $this->properties->extension);

		return Str::finish($base_path, '/').$file_name;
	}

}