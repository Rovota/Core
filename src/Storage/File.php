<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage;

use Rovota\Core\Storage\Interfaces\DiskInterface;
use Rovota\Core\Storage\Traits\FileFunctions;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Str;
use Rovota\Core\Support\Traits\Conditionable;

class File
{
	use FileFunctions, Conditionable;

	public mixed $contents = null;

	public string|null $name = null;
	public string|null $path = null;
	public DiskInterface|null $disk = null;
	public int $size = 0;
	public string|null $extension = null;
	public string|null $mime_type = 'text/html';
	public Moment|null $last_modified = null;

	protected bool $unsaved_changes = false;

	// -----------------

	public function __construct(mixed $contents, array $properties)
	{
		$this->contents = $contents;

		foreach ($properties as $key => $value) {
			if ($key === 'last_modified') {
				$this->last_modified = $value instanceof Moment ? $value : moment($value);
				continue;
			}
			if ($key === 'name') {
				$this->extension = Str::afterLast($value, '.');
			}
			$this->{$key} = $value;
		}
	}

	public function __toString(): string
	{
		return $this->contents;
	}

	// -----------------

	public function publicUrl(): string
	{
		return Str::finish($this->disk->baseUrl().$this->path, '/').$this->name;
	}

	public function properties(): array
	{
		return [
			'name' => $name ?? $this->name,
			'path' => $this->path,
			'disk' => $this->disk,
			'size' => $this->size,
			'extension' => $this->extension,
			'mime_type' => $this->mime_type,
			'last_modified' => $this->last_modified,
		];
	}

	// -----------------

	public static function make(mixed $contents, array $properties): File
	{
		return new File($contents, $properties);
	}

}