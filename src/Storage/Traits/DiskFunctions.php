<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage\Traits;

use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Rovota\Core\Storage\Directory;
use Rovota\Core\Storage\File;
use Rovota\Core\Storage\Interfaces\DirectoryInterface;
use Rovota\Core\Storage\Interfaces\FileInterface;
use Rovota\Core\Structures\Sequence;
use Rovota\Core\Support\ImageObject;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Str;
use SplFileInfo;
use Throwable;
use ZipArchive;

trait DiskFunctions
{

	public function asImage(string $location): ImageObject|null
	{
		return $this->retrieveFileWithFields($location, [])?->asImage();
	}

	public function asHash(string $location, string $algo = 'sha256', bool $binary = false): string|null
	{
		return $this->retrieveFileWithFields($location, [])?->asHash($algo, $binary);
	}

	public function asString(string $location): string
	{
		return $this->retrieveFileWithFields($location, [])?->asString() ?? '';
	}

	// -----------------

	public function contents(string $location = '/'): Sequence
	{
		$listing = [];
		foreach ($this->flysystem->listContents($location, false) as $item) {
			$listing[] = $item;
		}
		return new Sequence($listing);
	}

	public function files(string $location = '/'): Sequence
	{
		$listing = [];
		foreach ($this->flysystem->listContents($location, false) as $item) {
			if ($item instanceof FileAttributes) {
				$listing[] = $item;
			}
		}
		return new Sequence($listing);
	}

	public function directories(string $location = '/'): Sequence
	{
		$listing = [];
		foreach ($this->flysystem->listContents($location, false) as $item) {
			if ($item instanceof DirectoryAttributes) {
				$listing[] = $item;
			}
		}
		return new Sequence($listing);
	}

	// -----------------

	public function exists(string $location): bool
	{
		return $this->flysystem->has($location);
	}

	public function missing(string $location): bool
	{
		return $this->flysystem->has($location) === false;
	}

	// -----------------

	public function checksum(string $location, array $config = []): string
	{
		return $this->flysystem->checksum($location, $config);
	}

	// -----------------

	public function file(string $location, array $without = [], bool $stream = false): FileInterface|null
	{
		$fields = ['size', 'mime_type', 'last_modified'];

		foreach ($without as $field) {
			if (($key = array_search($field, $fields)) !== false) {
				unset($fields[$key]);
			}
		}

		return $this->retrieveFileWithFields($location, $fields, $stream);
	}

	public function directory(string $location): DirectoryInterface|null
	{
		return $this->retrieveDirectoryWithFields($location);
	}

	public function read(string $location): string
	{
		return $this->flysystem->read($location);
	}

	public function readStream(string $location): mixed
	{
		return $this->flysystem->readStream($location);
	}

	// -----------------

	public function write(string $location, string $contents): void
	{
		$this->flysystem->write($location, $contents);
	}

	public function writeStream(string $location, mixed $contents): void
	{
		$this->flysystem->writeStream($location, $contents);
	}

	// -----------------

	public function compress(string $source, string|null $target = null): FileInterface|null
	{
		$archive = new ZipArchive();
		$data = $this->getDataForCompression($source, $target);
		$random_name = Str::random(60).'.zip';

		if ($data['source_type'] === null) {
			return null;
		}

		if ($archive->open($this->config->root.'/'.$random_name, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {

			if ($data['source_type'] === 'directory') {
				/** @var SplFileInfo[] $files */
				$files = new RecursiveIteratorIterator(
					new RecursiveDirectoryIterator($data['source']),
					RecursiveIteratorIterator::LEAVES_ONLY
				);

				foreach ($files as $file) {
					if ($file->isDir() === false) {
						$file_path = $file->getRealPath();
						$relative_path = substr($file_path, mb_strlen($data['source']) + 1);
						$archive->addFile($file_path, $relative_path);
					}
				}
			}

			if ($data['source_type'] === 'file') {
				$file_path = $data['source'];
				$relative_path = basename($data['source']);
				$archive->addFile($file_path, $relative_path);
			}

		} else {
			return null;
		}

		$archive->close();
		$this->flysystem->move($random_name, $data['target_disk_path']);

		return $this->file($data['target_disk_path']);
	}

	public function extract(string $source, string|null $target = null): DirectoryInterface|null
	{
		$archive = new ZipArchive();
		$source = getcwd().'/'.$this->config->root.'/'.$source;
		$new_target = $target !== null ? getcwd().'/'.$this->config->root.'/'.$target : str_replace(basename($source), '', $source);

		if ($archive->open($source)) {
			$archive->extractTo($new_target);
			$archive->close();
		}
		return $this->directory(trim($target ?? '/', '/'));
	}

	// -----------------

	public function move(string $from, string $to): void
	{
		$this->flysystem->move($from, $to);
	}

	public function rename(string $location, string $name): void
	{
		$target = str_replace(basename($location), $name, $location);
		$this->flysystem->move($location, $target);
	}

	public function copy(string $from, string $to): void
	{
		$this->flysystem->copy($from, $to);
	}

	// -----------------

	public function delete(string $location): void
	{
		$this->flysystem->delete($location);
	}

	public function deleteDirectory(string $location): void
	{
		$this->flysystem->deleteDirectory($location);
	}

	public function clear(string $location): void
	{
		$this->flysystem->write($location, '');
	}

	public function clearDirectory(string $location): void
	{
		$contents = $this->flysystem->listContents($location);
		foreach ($contents as $item) {
			match($item['type']) {
				'file' => $this->flysystem->delete($item['path']),
				'dir' => $this->flysystem->deleteDirectory($item['path']),
			};
		}
	}

	// -----------------

	public function prepend(string $location, string $contents, bool $new_line = true): void
	{
		if ($this->missing($location)) {
			$this->write($location, $contents);
		} else {
			$this->retrieveFileWithFields($location, [])?->prepend($contents, $new_line)->save();
		}
	}

	public function append(string $location, string $contents, bool $new_line = true): void
	{
		if ($this->missing($location)) {
			$this->write($location, $contents);
		} else {
			$this->retrieveFileWithFields($location, [])?->append($contents, $new_line)->save();
		}
	}

	public function findAndReplace(string $location, array|string $search, array|string $replace): int
	{
		return $this->retrieveFileWithFields($location, [])?->findAndReplace($search, $replace, true) ?? 0;
	}

	// -----------------

	public function lastModified(string $location): Moment|null
	{
		return $this->retrieveFileWithFields($location, ['last_modified'])?->properties()->last_modified;
	}

	public function size(string $location): int
	{
		return $this->retrieveFileWithFields($location, ['size'])?->properties()->size;
	}

	public function mimeType(string $location): string|null
	{
		return $this->retrieveFileWithFields($location, ['mime_type'])?->properties()->mime_type;
	}

	// -----------------

	public function isExtension(string $location, string $extension): bool
	{
		return $this->retrieveFileWithFields($location, [])?->isExtension($extension) ?? false;
	}

	public function isAnyExtension(string $location, array $extensions): bool
	{
		return $this->retrieveFileWithFields($location, [])?->isAnyExtension($extensions) ?? false;
	}

	// -----------------

	public function isMimeType(string $location, string $mime_type): bool
	{
		return $this->retrieveFileWithFields($location, ['mime_type'])?->isMimeType($mime_type) ?? false;
	}

	public function isAnyMimeType(string $location, array $mime_types): bool
	{
		return $this->retrieveFileWithFields($location, ['mime_type'])?->isAnyMimeType($mime_types) ?? false;
	}

	// -----------------

	public function isEqual(string $first, string $second): bool
	{
		try {
			$first = $this->read($first);
			$second = $this->read($second);
		} catch (Throwable) {
			return false;
		}
		return hash_equals($first, $second);
	}

	// -----------------

	/**
	 * @throws UnableToReadFile
	 * @throws UnableToRetrieveMetadata
	 * @throws FilesystemException
	 */
	protected function retrieveFileWithFields(string $location, array $fields, bool $stream = false): FileInterface|null
	{
		$contents = $stream ? $this->flysystem->readStream($location) : $this->flysystem->read($location);
		$extension = pathinfo($location, PATHINFO_EXTENSION);

		$field_data = [
			'name' => basename($location),
			'path' => str_replace(basename($location), '', $location),
			'extension' => $extension,
			'disk' => $this,
		];

		foreach ($fields as $field) {
			$field_data[$field] = match($field) {
				'size' => $this->flysystem->fileSize($location),
				'mime_type' => sanitize_mime_type($extension, $this->flysystem->mimeType($location)),
				'last_modified' => $this->flysystem->lastModified($location),
			};
		}

		return File::make($contents, $field_data);
	}

	/**
	 * @throws UnableToCheckExistence
	 * @throws FilesystemException
	 */
	protected function retrieveDirectoryWithFields(string $location): DirectoryInterface|null
	{
		if ($this->flysystem->directoryExists($location) === false) {
			return null;
		}

		$field_data = [
			'name' => basename($location),
			'path' => str_replace(basename($location), '', $location),
			'disk' => $this,
		];

		return Directory::make($field_data);
	}

	// -----------------

	/**
	 * @throws UnableToCheckExistence
	 * @throws FilesystemException
	 */
	protected function getDataForCompression(string $source, string|null $target = null): array
	{
		$source_type = match(true) {
			$this->flysystem->directoryExists($source) => 'directory',
			$this->flysystem->fileExists($source) => 'file',
			default => null,
		};

		$source = trim(getcwd().'/'.$this->config->root.'/'.$source, '/');

		if ($target === null) {
			$target_filename = Str::finish(basename($source), '.zip');
			$target_real_path = trim(str_replace(basename($source), '', $source), '/').'/'.$target_filename;
		} else {
			$target = trim(getcwd().'/'.$this->config->root.'/'.$target, '/');
			$target_filename = Str::finish(basename($target), '.zip');
			$target_real_path = trim(str_replace(basename($target), '', $target), '/').'/'.$target_filename;
		}

		$target_disk_path = trim(Str::afterLast($target_real_path, $this->config->root), '/');

		return [
			'source' => $source,
			'source_type' => $source_type,
			'target_filename' => $target_filename,
			'target_real_path' => $target_real_path,
			'target_disk_path' => $target_disk_path,
		];
	}

}