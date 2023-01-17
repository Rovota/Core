<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage;

use Rovota\Core\Storage\Interfaces\DiskInterface;
use Rovota\Core\Support\Config;
use Rovota\Core\Support\Moment;

/**
 * @property string|null $name
 * @property string|null $path
 * @property DiskInterface|null $disk
 * @property int $size
 * @property string|null $extension
 * @property string|null $mime_type
 * @property Moment|null $last_modified
 */
final class FileProperties extends Config
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

	protected function size(): int
	{
		return $this->int('size');
	}

	protected function extension(): string|null
	{
		return $this->get('extension');
	}

	protected function mimeType(): string|null
	{
		return $this->get('mime_type', 'text/plain');
	}

	protected function lastModified(): Moment
	{
		return $this->moment('last_modified');
	}

}