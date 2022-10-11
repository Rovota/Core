<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Storage\Traits;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use Rovota\Core\Storage\Directory;
use Rovota\Core\Storage\File;
use Rovota\Core\Storage\Media;
use Rovota\Core\Support\ImageObject;
use Rovota\Core\Support\Text;
use Rovota\Core\Support\Traits\Conditionable;

trait FileFunctions
{
	use Conditionable;

	// -----------------

	/**
	 * This method requires the ImageMagick extension.
	 */
	public function asImage(): ImageObject|null
	{
		if (extension_loaded('imagick') === false || $this->contents === null) {
			return null;
		}
		return new ImageObject($this->asString(), $this->extension, $this->mime_type);
	}

	public function asMedia(): Media|null
	{
		if ($this->contents === null) {
			return null;
		}
		return Media::createUsing($this, $this->properties());
	}

	public function asHash(string $algo = 'sha256', bool $binary = false): string|null
	{
		if ($this->contents === null) {
			return null;
		}
		return hash($algo, $this->asString(), $binary);
	}

	public function asString(): string
	{
		if (is_resource($this->contents)) {
			return stream_get_contents($this->contents);
		}
		return $this->contents ?? '';
	}

	// -----------------

	/**
	 * @throws UnableToCheckExistence
	 * @throws UnableToReadFile
	 * @throws UnableToRetrieveMetadata
	 * @throws FilesystemException
	 */
	public function compress(string|null $target = null): File|null
	{
		return $this->disk->compress($this->location(), $target);
	}

	/**
	 * This functionality is currently only available for disks using the 'local' driver. When the target isn't specified, the archive will be extracted to the same folder as the archive.
	 * @throws UnableToReadFile
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public function extract(string|null $target = null): Directory|null
	{
		return $this->disk->extract($this->location(), $target);
	}

	// -----------------

	/**
	 * @throws UnableToMoveFile
	 * @throws FilesystemException
	 */
	public function move(string $to): static
	{
		$this->disk->move($this->location(), $to);
		$this->name = basename($to);
		$this->path = str_replace(basename($to), '', $to);
		return $this;
	}

	/**
	 * @throws UnableToMoveFile
	 * @throws FilesystemException
	 */
	public function rename(string $name): static
	{
		$this->disk->rename($this->location(), $name);
		$this->name = $name;
		return $this;
	}

	/**
	 * @throws UnableToCopyFile
	 * @throws FilesystemException
	 */
	public function copy(string $to): static
	{
		$this->disk->copy($this->location(), $to);
		return $this->disk->file($to);
	}

	// -----------------

	/**
	 * @throws UnableToDeleteFile
	 * @throws FilesystemException
	 */
	public function delete(): void
	{
		$this->disk->delete($this->location());
	}

	public function clear(): static
	{
		$this->contents = '';
		$this->unsaved_changes = true;
		return $this;
	}

	// -----------------

	/**
	 * A new line will not be used when the existing content is empty.
	 */
	public function prepend(string $contents, bool $new_line = true): static
	{
		$new_line = empty($this->asString()) === false && $new_line === true;
		$this->contents = Text::finish($contents, $new_line ? "\n" : '').$this->asString();
		$this->unsaved_changes = true;
		return $this;
	}

	/**
	 * A new line will not be used when the existing content is empty.
	 */
	public function append(string $contents, bool $new_line = true): static
	{
		$new_line = empty($this->asString()) === false && $new_line === true;
		$this->contents = Text::finish($this->asString(), $new_line ? "\n" : '').$contents;
		$this->unsaved_changes = true;
		return $this;
	}

	public function findAndReplace(array|string $search, array|string $replace, bool $count = false): static|int
	{
		$this->contents = str_replace($search, $replace, $this->asString(), $count);
		$this->unsaved_changes = true;
		return $count ?: $this;
	}

	// -----------------

	public function isExtension(string $extension): bool
	{
		return $this->extension === $extension;
	}

	public function isAnyExtension(array $extensions): bool
	{
		foreach ($extensions as $extension) {
			if ($this->isExtension($extension)) {
				return true;
			}
		}
		return false;
	}

	// -----------------

	public function isMimeType(string $mime_type): bool
	{
		return $this->mime_type === $mime_type;
	}

	public function isAnyMimeType(array $mime_types): bool
	{
		foreach ($mime_types as $mime_type) {
			if ($this->isMimeType($mime_type)) {
				return true;
			}
		}
		return false;
	}

	// -----------------

	/**
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public function save(): static
	{
		if ($this->unsaved_changes) {
			if (is_resource($this->contents)) {
				$this->disk->writeStream($this->location(), $this->contents);
				return $this;
			}
			$this->disk->write($this->location(), $this->asString());
		}
		return $this;
	}

	// -----------------

	protected function location(): string
	{
		return ($this->path ?? '').($this->name ?? '');
	}

}