<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage\Interfaces;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use Rovota\Core\Storage\Directory;
use Rovota\Core\Storage\File;
use Rovota\Core\Storage\Media;
use Rovota\Core\Support\Collection;
use Rovota\Core\Support\ImageObject;
use Rovota\Core\Support\Moment;

interface DiskInterface
{

	public function isDefault(): bool;

	// -----------------

	public function name(): string;

	// -----------------

	public function option(string $name): mixed;

	public function driver(): string;

	public function readOnly(): bool;

	public function label(): string;

	public function root(): string;

	public function domain(): string;

	// -----------------

	public function baseUrl(): string;

	// -----------------

	/**
	 * This method requires the ImageMagick extension.
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public function asImage(string $location): ImageObject|null;

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public function asMedia(string $location): Media|null;

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public function asHash(string $location, string $algo = 'sha256', bool $binary = false): string|null;

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public function asString(string $location): string;

	// -----------------

	/**
	 * @throws FilesystemException
	 */
	public function contents(string $location = '/'): Collection;

	/**
	 * @throws FilesystemException
	 */
	public function files(string $location = '/'): Collection;

	/**
	 * @throws FilesystemException
	 */
	public function directories(string $location = '/'): Collection;

	// -----------------

	/**
	 * @throws UnableToCheckExistence
	 * @throws FilesystemException
	 */
	public function exists(string $location): bool;

	/**
	 * @throws UnableToCheckExistence
	 * @throws FilesystemException
	 */
	public function missing(string $location): bool;

	// -----------------

	public function checksum(string $location, array $config = []): string;

	// -----------------

	/**
	 * @throws UnableToReadFile
	 * @throws UnableToRetrieveMetadata
	 * @throws FilesystemException
	 */
	public function file(string $location, array $without = [], bool $stream = false): File|null;

	/**
	 * @throws UnableToCheckExistence
	 * @throws FilesystemException
	 */
	public function directory(string $location): Directory|null;

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public function read(string $location): string;

	/**
	 * @return resource
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public function readStream(string $location): mixed;

	// -----------------

	/**
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public function write(string $location, string $contents): void;

	/**
	 * @param string $location
	 * @param mixed $contents
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public function writeStream(string $location, mixed $contents): void;

	// -----------------

	/**
	 * @throws UnableToCheckExistence
	 * @throws UnableToReadFile
	 * @throws UnableToRetrieveMetadata
	 * @throws FilesystemException
	 */
	public function compress(string $source, string|null $target = null): File|null;

	/**
	 * This functionality is currently only available for disks using the 'local' driver. When the target isn't specified, the archive will be extracted to the same folder as the archive.
	 * @throws UnableToReadFile
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public function extract(string $source, string|null $target = null): Directory|null;

	// -----------------

	/**
	 * @throws UnableToMoveFile
	 * @throws FilesystemException
	 */
	public function move(string $from, string $to): void;

	/**
	 * @throws UnableToMoveFile
	 * @throws FilesystemException
	 */
	public function rename(string $location, string $name): void;

	/**
	 * @throws UnableToCopyFile
	 * @throws FilesystemException
	 */
	public function copy(string $from, string $to): void;

	// -----------------

	/**
	 * @throws UnableToDeleteFile
	 * @throws FilesystemException
	 */
	public function delete(string $location): void;

	/**
	 * @throws UnableToDeleteDirectory
	 * @throws FilesystemException
	 */
	public function deleteDirectory(string $location): void;

	/**
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public function clear(string $location): void;

	/**
	 * @throws UnableToDeleteFile
	 * @throws UnableToDeleteDirectory
	 * @throws FilesystemException
	 */
	public function clearDirectory(string $location): void;

	// -----------------

	/**
	 * A new line will not be used when the existing content is empty.
	 * @throws UnableToReadFile
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public function prepend(string $location, string $contents, bool $new_line = true): void;

	/**
	 * A new line will not be used when the existing content is empty.
	 * @throws UnableToReadFile
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public function append(string $location, string $contents, bool $new_line = true): void;

	/**
	 * @throws UnableToReadFile
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public function findAndReplace(string $location, array|string $search, array|string $replace): int;

	// -----------------

	/**
	 * @throws UnableToRetrieveMetadata
	 * @throws FilesystemException
	 */
	public function lastModified(string $location): Moment|null;

	/**
	 * @throws UnableToRetrieveMetadata
	 * @throws FilesystemException
	 */
	public function size(string $location): int;

	/**
	 * @throws UnableToRetrieveMetadata
	 * @throws FilesystemException
	 */
	public function mimeType(string $location): string|null;

	// -----------------

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public function isExtension(string $location, string $extension): bool;

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public function isAnyExtension(string $location, array $extensions): bool;

	// -----------------

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public function isMimeType(string $location, string $mime_type): bool;

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public function isAnyMimeType(string $location, array $mime_types): bool;

	// -----------------

	/**
	 * If either file does not exist, false will be returned.
	 */
	public function isEqual(string $first, string $second): bool;

	// -----------------

	/**
	 * Provides access to the underlying Flysystem implementation, in case some functionality isn't accessible otherwise.
	 */
	public function flysystem(): Filesystem;

}