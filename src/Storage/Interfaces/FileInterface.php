<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage\Interfaces;


use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use Rovota\Core\Storage\FileProperties;
use Rovota\Core\Support\ImageObject;

interface FileInterface
{

	public static function make(mixed $contents, array $properties): FileInterface;

	// -----------------

	public function contents(): mixed;

	public function properties(): FileProperties;

	public function publicUrl(): string;

	// -----------------

	/**
	 * Executes the provided callback when the condition is `true`. Optionally, when `false`, the alternative callback will be executed.
	 */
	public function when(mixed $condition, callable $callback, callable|null $alternative = null): static;

	/**
	 * Executes the provided callback when the condition is `false`. Optionally, when `true`, the alternative callback will be executed.
	 */
	public function unless(mixed $condition, callable $callback, callable|null $alternative = null): static;

	// -----------------

	/**
	 * This method requires the ImageMagick extension.
	 */
	public function asImage(): ImageObject|null;

	public function asHash(string $algo = 'sha256', bool $binary = false): string|null;

	public function asString(): string;

	// -----------------

	public function write(mixed $content): static;

	// -----------------

	/**
	 * @throws UnableToCheckExistence
	 * @throws UnableToReadFile
	 * @throws UnableToRetrieveMetadata
	 * @throws FilesystemException
	 */
	public function compress(string|null $target = null): FileInterface|null;

	/**
	 * This functionality is currently only available for disks using the 'local' driver. When the target isn't specified, the archive will be extracted to the same folder as the archive.
	 * @throws UnableToReadFile
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public function extract(string|null $target = null): DirectoryInterface|null;

	// -----------------

	public function checksum(array $config = []): string;

	// -----------------

	/**
	 * @throws UnableToMoveFile
	 * @throws FilesystemException
	 */
	public function move(string $to): static;

	/**
	 * @throws UnableToMoveFile
	 * @throws FilesystemException
	 */
	public function rename(string $name): static;

	/**
	 * @throws UnableToCopyFile
	 * @throws FilesystemException
	 */
	public function copy(string $to): static;

	// -----------------

	/**
	 * @throws UnableToDeleteFile
	 * @throws FilesystemException
	 */
	public function delete(): void;

	public function clear(): static;

	// -----------------

	/**
	 * A new line will not be used when the existing content is empty.
	 */
	public function prepend(string $content, bool $new_line = true): static;

	/**
	 * A new line will not be used when the existing content is empty.
	 */
	public function append(string $content, bool $new_line = true): static;

	public function findAndReplace(array|string $search, array|string $replace, bool $count = false): static|int;

	// -----------------

	public function isExtension(string $extension): bool;

	public function isAnyExtension(array $extensions): bool;

	// -----------------

	public function isMimeType(string $mime_type): bool;

	public function isAnyMimeType(array $mime_types): bool;

	// -----------------

	/**
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public function save(): static;

}