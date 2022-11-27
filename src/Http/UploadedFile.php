<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http;

use ImagickException;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToWriteFile;
use Rovota\Core\Facades\Registry;
use Rovota\Core\Http\Enums\UploadError;
use Rovota\Core\Storage\File;
use Rovota\Core\Storage\Interfaces\DiskInterface;
use Rovota\Core\Storage\StorageManager;
use Rovota\Core\Support\ImageObject;
use Rovota\Core\Support\Str;
use SplFileInfo;
use const PATHINFO_EXTENSION;

class UploadedFile extends SplFileInfo
{

	protected array $untrusted = [];

	public bool $validated = false;

	/**
	 * @var array<string, File>
	 */
	public array $variants = [];

	// -----------------

	public function __construct(string $name, string $type, string $temp_name, int $error)
	{
		$this->untrusted = [
			'name' => basename(trim($name)),
			'type' => trim($type),
			'temp_name' => $temp_name,
			'error' => $error,
		];

		parent::__construct($temp_name);

		if ($this->untrusted['error'] === UPLOAD_ERR_OK && is_uploaded_file($this->getPathname()) && $this->getSize() > 0) {
			$this->variants['original'] = $this->createFileFromUntrustedData($this->untrusted);
			$this->validated = true;
		}
	}

	// -----------------

	public function variant(string $name): File|null
	{
		return $this->variants[$name];
	}

	// -----------------

	public function originalFileName(): string
	{
		return $this->untrusted['name'];
	}

	public function originalExtension(): string
	{
		return pathinfo($this->untrusted['name'], PATHINFO_EXTENSION);
	}

	public function originalMimeType(): string
	{
		return $this->untrusted['type'];
	}

	// -----------------

	public function error(): UploadError|null
	{
		return UploadError::tryFrom($this->untrusted['error']);
	}

	public function errorMessage(): string
	{
		$error = UploadError::tryFrom($this->untrusted['error']);
		return $error !== null ? $error->message() : 'The file "%s" was not uploaded due to an unknown error.';
	}

	// -----------------

	/**
	 * This method requires the ImageMagick extension in order to resize, compress and reformat images.
	 * @throws UnableToWriteFile
	 * @throws ImagickException
	 * @throws FilesystemException
	 */
	public function store(string $path, DiskInterface|string|null $disk = null, array $variants = []): bool
	{
		if ($this->validated === false) {
			return false;
		}

		$this->variants['original']->path = Str::finish(trim($path), '/');
		$this->variants['original']->disk = $disk instanceof DiskInterface ? $disk : StorageManager::get($disk);

		if (empty($variants)) {
			$this->variants['original']->save();
			return true;
		}

		// If variants are given, treat it as image.
		if ($variants === ['*']) {
			$variants = ['original', 'thumbs', 'medium', 'small', 'large'];
		}

		$content = $this->variants['original']->asString();
		$extension = $this->variants['original']->extension;
		$mime_type = $this->variants['original']->mime_type;

		foreach ($variants as $variant) {

			if ($variant === 'original') {
				$this->variants['original']->save();
				continue;
			}

			// Quit trying if imagick isn't available.
			if (extension_loaded('imagick') === false) {
				return false;
			}

			$image = new ImageObject($content, $extension, $mime_type);

			$properties = $this->variants['original']->properties();
			$properties['path']  = $properties['path'].$variant.'/';

			// Convert to WebP on-the-fly
			if (Registry::bool('media_convert_to_webp', true)) {
				$properties['name'] = str_replace($properties['extension'], 'webp', $properties['name']);
				$properties['mime_type'] = 'image/webp';
				$properties['extension'] = 'webp';
				$image->mime_type = 'image/webp';
				$image->format('webp');
			}

			// Remove EXIF data
			if (Registry::bool('media_preserve_exif', true) === false) {
				$image->removeExif();
			}

			// Resize to fit height and width
			$dimensions = Registry::array('media_size_'.$variant);
			if (empty($dimensions) === false) {
				$image->resize($dimensions[0], $dimensions[1]);
			}

			// Compress
			$compression_level = Registry::int('media_compression_level', 90);
			$image->compress($compression_level > 0 ? $compression_level : 98);

			// Save
			$this->variants[$variant] = File::make($image->extract(), $properties);
			$this->variants[$variant]->save();

		}

		return true;
	}

	// -----------------

	/**
	 * This method requires the ImageMagick extension in order to resize, compress and reformat images.
	 * @throws UnableToWriteFile
	 * @throws ImagickException
	 * @throws FilesystemException
	 */
	public function storeAs(string $path, string $name, DiskInterface|string|null $disk = null, array $variants = ['original']): bool
	{
		$this->variants['original']->name = $name;
		return $this->store($path, $disk, $variants);
	}

	// -----------------

	protected function createFileFromUntrustedData(array $untrusted): File|null
	{
		$contents = fopen($untrusted['temp_name'], 'r');

		$mime_type = $this->processMimeType($untrusted['temp_name']);
		$extension = pathinfo($untrusted['name'], PATHINFO_EXTENSION);
		$extension = sanitize_extension($mime_type, $extension) ?? '.txt';
		$name = $this->processFilename($untrusted['name'], $extension);

		return File::make($contents, [
			'name' => $name,
			'path' => '/',
			'disk' => StorageManager::get(),
			'size' => $this->getSize(),
			'extension' => $extension,
			'mime_type' => $mime_type,
			'last_modified' => now(),
		]);
	}

	// -----------------

	protected function processMimeType(string $temp_name): string
	{
		$info = finfo_open(FILEINFO_MIME_TYPE);
		if ($info !== false) {
			$mime_type = finfo_file($info, $temp_name);
			finfo_close($info);
		} else {
			$mime_type = 'application/octet-stream';
		}
		return $mime_type;
	}

	protected function processFilename(string $name, string $extension): string
	{
		$name = str_replace('.'.$extension, '', $name);
		return sprintf('%s.%s', $name, $extension);
	}

}