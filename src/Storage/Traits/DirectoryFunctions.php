<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage\Traits;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use Rovota\Core\Storage\File;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Traits\Conditionable;

trait DirectoryFunctions
{
	use Conditionable;

	// -----------------

	/**
	 * @throws FilesystemException
	 */
	public function contents(): Bucket
	{
		return $this->disk->contents($this->location());
	}

	/**
	 * @throws FilesystemException
	 */
	public function files(): Bucket
	{
		return $this->disk->files($this->location());
	}

	/**
	 * @throws FilesystemException
	 */
	public function directories(): Bucket
	{
		return $this->disk->directories($this->location());
	}

	// -----------------

	/**
	 * @throws UnableToCheckExistence
	 * @throws FilesystemException
	 */
	public function exists(string $location): bool
	{
		return $this->disk->exists($this->location().'/'.$location);
	}

	/**
	 * @throws UnableToCheckExistence
	 * @throws FilesystemException
	 */
	public function missing(string $location): bool
	{
		return $this->disk->missing($this->location().'/'.$location);
	}

	// -----------------

	public function checksum(string $location, array $config = []): string
	{
		return $this->disk->checksum($this->location().'/'.$location, $config);
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
		return $this->disk->directory($to);
	}

	// -----------------

	/**
	 * @throws UnableToDeleteFile
	 * @throws FilesystemException
	 */
	public function delete(): void
	{
		$this->disk->deleteDirectory($this->location());
	}

	/**
	 * @throws UnableToDeleteFile
	 * @throws UnableToDeleteDirectory
	 * @throws FilesystemException
	 */
	public function clear(): void
	{
		$this->disk->clearDirectory($this->location());
	}

	// -----------------

	protected function location(): string
	{
		return ($this->path ?? '').($this->name ?? '');
	}

}