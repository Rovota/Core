<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Storage;

use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemException;
use League\Flysystem\ReadOnly\ReadOnlyFilesystemAdapter;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Rovota\Core\Kernel\Application;
use Rovota\Core\Storage\Interfaces\DiskInterface;
use Rovota\Core\Support\Collection;
use Rovota\Core\Support\ImageObject;
use Rovota\Core\Support\Text;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Traits\Conditionable;
use SplFileInfo;
use Throwable;
use ZipArchive;

abstract class Disk implements DiskInterface
{
	use Conditionable;

	protected string $name;
	protected array $options;
	protected Filesystem $flysystem;

	// -----------------

	public function __construct(string $name, FilesystemAdapter $adapter, array $options = [])
	{
		$this->name = $name;
		$this->options = $options;

		$this->flysystem = new Filesystem($this->readOnly() ? new ReadOnlyFilesystemAdapter($adapter) : $adapter);
	}

	// -----------------

	public function isDefault(): bool
	{
		return StorageManager::getDefault() === $this->name;
	}

	// -----------------

	public function name(): string
	{
		return $this->name;
	}

	// -----------------

	public function option(string $name): string|bool|int|array|null
	{
		return $this->options[$name] ?? null;
	}

	public function driver(): string
	{
		return $this->option('driver');
	}

	public function readOnly(): bool
	{
		return $this->option('read_only') ?? false;
	}

	public function label(): string
	{
		return $this->option('label');
	}

	public function root(): string
	{
		return $this->option('root');
	}

	public function domain(): string
	{
		$domain = $this->option('domain');
		$fallback = Application::$server->get('HTTP_HOST');

		if (is_array($domain)) {
			return $domain[Application::getEnvironment()] ?? $fallback;
		}

		return $domain ?? Application::$server->get('HTTP_HOST');
	}

	// -----------------

	public function baseUrl(): string
	{
		$root = Text::startAndFinish($this->root(), '/');
		$scheme = Application::$server->get('REQUEST_SCHEME', 'https');

		return sprintf('%s://%s%s', $scheme, $this->domain(), $root);
	}

	// -----------------

	public function __toString(): string
	{
		return $this->name;
	}

	public function __get(string $name): mixed
	{
		return $this->options[$name] ?? null;
	}

	public function __isset(string $name): bool
	{
		return isset($this->options[$name]);
	}

	// -----------------

	public function asImage(string $location): ImageObject|null
	{
		return $this->retrieveFileWithFields($location, [])?->asImage();
	}

	public function asMedia(string $location): Media|null
	{
		return $this->retrieveFileWithFields($location, [])?->asMedia();
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

	public function contents(string $location = '/'): Collection
	{
		$listing = [];
		foreach ($this->flysystem->listContents($location, false) as $item) {
			$listing[] = $item;
		}
		return new Collection($listing);
	}

	public function files(string $location = '/'): Collection
	{
		$listing = [];
		foreach ($this->flysystem->listContents($location, false) as $item) {
			if ($item instanceof FileAttributes) {
				$listing[] = $item;
			}
		}
		return new Collection($listing);
	}

	public function directories(string $location = '/'): Collection
	{
		$listing = [];
		foreach ($this->flysystem->listContents($location, false) as $item) {
			if ($item instanceof DirectoryAttributes) {
				$listing[] = $item;
			}
		}
		return new Collection($listing);
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

	public function file(string $location, array $without = [], bool $stream = false): File|null
	{
		$fields = ['size', 'mime_type', 'last_modified'];

		foreach ($without as $field) {
			if (($key = array_search($field, $fields)) !== false) {
				unset($fields[$key]);
			}
		}

		return $this->retrieveFileWithFields($location, $fields, $stream);
	}

	public function directory(string $location): Directory|null
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

	public function compress(string $source, string|null $target = null): File|null
	{
		$archive = new ZipArchive();
		$data = $this->getDataForCompression($source, $target);
		$random_name = Text::random(60).'.zip';

		if ($data['source_type'] === null) {
			return null;
		}

		if ($archive->open($this->root().'/'.$random_name, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {

			if ($data['source_type'] === 'directory') {
				/** @var SplFileInfo[] $files */
				$files = new RecursiveIteratorIterator(
					new RecursiveDirectoryIterator($data['source']),
					RecursiveIteratorIterator::LEAVES_ONLY
				);

				foreach ($files as $file) {
					if ($file->isDir() === false) {
						$file_path = $file->getRealPath();
						$relative_path = substr($file_path, strlen($data['source']) + 1);
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

	public function extract(string $source, string|null $target = null): Directory|null
	{
		$archive = new ZipArchive();
		$source = getcwd().'/'.$this->root().'/'.$source;
		$new_target = $target !== null ? getcwd().'/'.$this->root().'/'.$target : str_replace(basename($source), '', $source);

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
		$this->retrieveFileWithFields($location, [])?->prepend($contents, $new_line)->save();
	}

	public function append(string $location, string $contents, bool $new_line = true): void
	{
		$this->retrieveFileWithFields($location, [])?->append($contents, $new_line)->save();
	}

	public function findAndReplace(string $location, array|string $search, array|string $replace): int
	{
		return $this->retrieveFileWithFields($location, [])?->findAndReplace($search, $replace, true) ?? 0;
	}

	// -----------------

	public function lastModified(string $location): Moment|null
	{
		return $this->retrieveFileWithFields($location, ['last_modified'])?->last_modified;
	}

	public function size(string $location): int
	{
		return $this->retrieveFileWithFields($location, ['size'])?->size;
	}

	public function mimeType(string $location): string|null
	{
		return $this->retrieveFileWithFields($location, ['size'])?->mime_type;
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

	public function flysystem(): Filesystem
	{
		return $this->flysystem;
	}

	// -----------------

	/**
	 * @throws UnableToReadFile
	 * @throws UnableToRetrieveMetadata
	 * @throws FilesystemException
	 */
	protected function retrieveFileWithFields(string $location, array $fields, bool $stream = false): File|null
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
	protected function retrieveDirectoryWithFields(string $location): Directory|null
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

		$source = trim(getcwd().'/'.$this->root().'/'.$source, '/');

		if ($target === null) {
			$target_filename = Text::finish(basename($source ?? $this->root()), '.zip');
			$target_real_path = trim(str_replace(basename($source), '', $source), '/').'/'.$target_filename;
		} else {
			$target = trim(getcwd().'/'.$this->root().'/'.$target, '/');
			$target_filename = Text::finish(basename($target), '.zip');
			$target_real_path = trim(str_replace(basename($target), '', $target), '/').'/'.$target_filename;
		}

		$target_disk_path = trim(Text::afterLast($target_real_path, $this->root()), '/');

		return [
			'source' => $source,
			'source_type' => $source_type,
			'target_filename' => $target_filename,
			'target_real_path' => $target_real_path,
			'target_disk_path' => $target_disk_path,
		];
	}

}