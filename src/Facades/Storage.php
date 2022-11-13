<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

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
use Rovota\Core\Storage\Interfaces\DiskInterface;
use Rovota\Core\Storage\Media;
use Rovota\Core\Storage\StorageManager;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\ImageObject;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Text;

final class Storage
{

	protected function __construct()
	{
	}

	// -----------------

	public static function disk(string $name): DiskInterface
	{
		return StorageManager::get($name);
	}

	/**
	 * @throws \Rovota\Core\Storage\Exceptions\UnsupportedDriverException
	 */
	public static function build(array $options, string|null $name = null): DiskInterface
	{
		return StorageManager::build($name ?? Text::random(20), $options);
	}

	// -----------------

	/**
	 * This method requires the ImageMagick extension.
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public static function asImage(string $location): ImageObject|null
	{
		return StorageManager::get()->asImage($location);
	}

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public static function asMedia(string $location): Media|null
	{
		return StorageManager::get()->asMedia($location);
	}

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public static function asHash(string $location, string $algo = 'sha256', bool $binary = false): string|null
	{
		return StorageManager::get()->asHash($location, $algo, $binary);
	}

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public static function asString(string $location): string
	{
		return StorageManager::get()->asString($location);
	}

	// -----------------

	/**
	 * @throws FilesystemException
	 */
	public static function contents(string $location = '/'): Bucket
	{
		return StorageManager::get()->contents($location);
	}

	/**
	 * @throws FilesystemException
	 */
	public static function files(string $location = '/'): Bucket
	{
		return StorageManager::get()->files($location);
	}

	/**
	 * @throws FilesystemException
	 */
	public static function directories(string $location = '/'): Bucket
	{
		return StorageManager::get()->directories($location);
	}

	// -----------------

	/**
	 * @throws UnableToCheckExistence
	 * @throws FilesystemException
	 */
	public static function exists(string $location): bool
	{
		return StorageManager::get()->exists($location);
	}

	/**
	 * @throws UnableToCheckExistence
	 * @throws FilesystemException
	 */
	public static function missing(string $location): bool
	{
		return StorageManager::get()->missing($location);
	}

	// -----------------

	public static function checksum(string $location, array $config = []): string
	{
		return StorageManager::get()->checksum($location, $config);
	}

	// -----------------

	/**
	 * @throws UnableToReadFile
	 * @throws UnableToRetrieveMetadata
	 * @throws FilesystemException
	 */
	public static function file(string $location, array $without = [], bool $stream = false): File|null
	{
		return StorageManager::get()->file($location, $without, $stream);
	}

	/**
	 * @throws UnableToCheckExistence
	 * @throws FilesystemException
	 */
	public static function directory(string $location): Directory|null
	{
		return StorageManager::get()->directory($location);
	}

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public static function read(string $location): string
	{
		return StorageManager::get()->read($location);
	}

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public static function readStream(string $location): mixed
	{
		return StorageManager::get()->readStream($location);
	}

	// -----------------

	/**
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public static function write(string $location, string $contents): void
	{
		StorageManager::get()->write($location, $contents);
	}

	/**
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public static function writeStream(string $location, mixed $contents): void
	{
		StorageManager::get()->writeStream($location, $contents);
	}

	// -----------------

	/**
	 * @throws UnableToCheckExistence
	 * @throws UnableToReadFile
	 * @throws UnableToRetrieveMetadata
	 * @throws FilesystemException
	 */
	public static function compress(string $source, string|null $target = null): File|null
	{
		return StorageManager::get()->compress($source, $target);
	}

	/**
	 * This functionality is currently only available for disks using the 'local' driver. When the target isn't specified, the archive will be extracted to the same folder as the archive.
	 * @throws UnableToReadFile
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public static function extract(string $source, string|null $target = null): Directory|null
	{
		return StorageManager::get()->extract($source, $target);
	}

	// -----------------

	/**
	 * @throws UnableToMoveFile
	 * @throws FilesystemException
	 */
	public static function move(string $from, string $to): void
	{
		StorageManager::get()->move($from, $to);
	}

	/**
	 * @throws UnableToMoveFile
	 * @throws FilesystemException
	 */
	public static function rename(string $location, string $name): void
	{
		StorageManager::get()->rename($location, $name);
	}

	/**
	 * @throws UnableToCopyFile
	 * @throws FilesystemException
	 */
	public static function copy(string $from, string $to): void
	{
		StorageManager::get()->copy($from, $to);
	}

	// -----------------

	/**
	 * @throws UnableToDeleteFile
	 * @throws FilesystemException
	 */
	public static function delete(string $location): void
	{
		StorageManager::get()->delete($location);
	}

	/**
	 * @throws UnableToDeleteDirectory
	 * @throws FilesystemException
	 */
	public static function deleteDirectory(string $location): void
	{
		StorageManager::get()->deleteDirectory($location);
	}

	/**
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public static function clear(string $location): void
	{
		StorageManager::get()->clear($location);
	}

	/**
	 * @throws UnableToDeleteFile
	 * @throws UnableToDeleteDirectory
	 * @throws FilesystemException
	 */
	public static function clearDirectory(string $location): void
	{
		StorageManager::get()->clearDirectory($location);
	}

	// -----------------

	/**
	 * A new line will not be used when the existing content is empty.
	 * @throws UnableToReadFile
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public static function prepend(string $location, string $contents, bool $new_line = true): void
	{
		StorageManager::get()->prepend($location, $contents, $new_line);
	}

	/**
	 * A new line will not be used when the existing content is empty.
	 * @throws UnableToReadFile
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public static function append(string $location, string $contents, bool $new_line = true): void
	{
		StorageManager::get()->append($location, $contents, $new_line);
	}

	/**
	 * @throws UnableToReadFile
	 * @throws UnableToWriteFile
	 * @throws FilesystemException
	 */
	public static function findAndReplace(string $location, array|string $search, array|string $replace): int
	{
		return StorageManager::get()->findAndReplace($location, $search, $replace);
	}

	// -----------------

	/**
	 * @throws UnableToRetrieveMetadata
	 * @throws FilesystemException
	 */
	public static function lastModified(string $location): Moment|null
	{
		return StorageManager::get()->lastModified($location);
	}

	/**
	 * @throws UnableToRetrieveMetadata
	 * @throws FilesystemException
	 */
	public static function size(string $location): int
	{
		return StorageManager::get()->size($location);
	}

	/**
	 * @throws UnableToRetrieveMetadata
	 * @throws FilesystemException
	 */
	public static function mimeType(string $location): string|null
	{
		return StorageManager::get()->mimeType($location);
	}

	// -----------------

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public static function isExtension(string $location, string $extension): bool
	{
		return StorageManager::get()->isExtension($location, $extension);
	}

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public static function isAnyExtension(string $location, array $extensions): bool
	{
		return StorageManager::get()->isAnyExtension($location, $extensions);
	}

	// -----------------

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public static function isMimeType(string $location, string $mime_type): bool
	{
		return StorageManager::get()->isMimeType($location, $mime_type);
	}

	/**
	 * @throws UnableToReadFile
	 * @throws FilesystemException
	 */
	public static function isAnyMimeType(string $location, array $mime_types): bool
	{
		return StorageManager::get()->isAnyMimeType($location, $mime_types);
	}

	// -----------------

	/**
	 * If either file does not exist, false will be returned.
	 */
	public static function isEqual(string $first, string $second): int
	{
		return StorageManager::get()->isEqual($first, $second);
	}

	// -----------------

	/**
	 * Provides access to the underlying Flysystem implementation, in case some functionality isn't accessible otherwise.
	 */
	public static function flysystem(): Filesystem
	{
		return StorageManager::get()->flysystem();
	}

}