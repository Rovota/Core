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
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use Rovota\Core\Storage\DirectoryProperties;
use Rovota\Core\Structures\Sequence;

interface DirectoryInterface
{

	public static function make(array $properties): DirectoryInterface;

	// -----------------

	public function properties(): DirectoryProperties;

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
	 * @throws FilesystemException
	 */
	public function contents(): Sequence;

	/**
	 * @throws FilesystemException
	 */
	public function files(): Sequence;

	/**
	 * @throws FilesystemException
	 */
	public function directories(): Sequence;

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
	 * @throws UnableToCheckExistence
	 * @throws UnableToReadFile
	 * @throws UnableToRetrieveMetadata
	 * @throws FilesystemException
	 */
	public function compress(string|null $target = null): FileInterface|null;

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

	/**
	 * @throws UnableToDeleteFile
	 * @throws UnableToDeleteDirectory
	 * @throws FilesystemException
	 */
	public function clear(): void;

}