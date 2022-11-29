<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage;

use League\Flysystem\UnableToDeleteFile;
use Rovota\Core\Database\Model;
use Rovota\Core\Http\UploadedFile;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Storage\Enums\MediaType;
use Rovota\Core\Storage\Interfaces\DiskInterface;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Arr;
use Rovota\Core\Support\ImageObject;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Str;
use Rovota\Core\Support\Traits\Metadata;
use Throwable;

/**
 * @property int $id
 * @property string $file_name
 * @property string|null $file_path
 * @property int $file_size
 * @property string|null $disk_name
 * @property string $extension
 * @property string $mime_type
 * @property int|null $folder_id
 * @property array|null $variants
 * @property int|null $uploader_id
 * @property MediaType $type
 * @property bool $managed
 * @property bool $pinned
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 *
 * @property-read DiskInterface $disk
 * @property-read MediaFolder|null $folder
 */
class Media extends Model
{
	use Metadata;

	protected string|null $table = 'media';

	protected array $attributes = [
		'file_size' => 0,
		'mime_type' => 'text/plain',
		'type' => MediaType::Unknown,
		'managed' => false,
		'pinned' => false,
	];

	protected array $casts = [
		'variants' => 'array',
		'type' => ['enum', MediaType::class],
		'managed' => 'bool',
		'pinned' => 'bool',
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	protected string $meta_model = MediaMeta::class;
	protected string $meta_foreign_key = 'media_id';

	protected string|null $content;

	/**
	 * @var array<string, File>
	 */
	protected array $files = [];

	// -----------------

	public function eventModelLoaded(): void
	{
		$this->loadMeta();
	}

	// -----------------

	public static function createUsing(UploadedFile|File $file, array $attributes = []): static
	{
		$variants = [];
		if ($file instanceof UploadedFile) {
			$variants = array_keys($file->variants);
			$file->variant('original');
		}

		$attributes = array_merge([
			'file_name' => $file->name,
			'file_path' => $file->path,
			'file_size' => $file->size,
			'disk_name' => $file->disk->name(),
			'extension' => $file->extension,
			'mime_type' => $file->mime_type,
			'variants' => $variants,
			'type' => MediaManager::getMediaType($file->mime_type),
		], $attributes);

		return new static($attributes);
	}

	// -----------------

	public function getFolderAttribute(): MediaFolder|null
	{
		return MediaManager::getOrLoadFolder($this->folder_id);
	}

	public function setFolderAttribute(MediaFolder|int|null $folder): void
	{
		if ($folder instanceof MediaFolder) {
			$this->folder_id = $folder->id;
		} else {
			if ($folder === null) {
				$this->folder_id = null;
			} else {
				$this->folder_id = $folder;
			}
		}
	}

	public function getDiskAttribute(): DiskInterface
	{
		return StorageManager::get($this->disk_name);
	}

	public function setDiskAttribute(DiskInterface|string $disk): void
	{
		$this->disk_name = $disk instanceof DiskInterface ? $disk->name() : $disk;
	}

	// -----------------

	public function asFile(string|null $variant = null): File|null
	{
		return $this->getFileForVariant($variant ?? 'original');
	}

	/**
	 * This method requires the ImageMagick extension.
	 */
	public function asImage(string|null $variant = null): ImageObject|null
	{
		return $this->getFileForVariant($variant ?? 'original')?->asImage();
	}

	public function asHash(string|null $variant = null, string $algo = 'sha256', bool $binary = false): string|null
	{
		return $this->getFileForVariant($variant ?? 'original')?->asHash($algo, $binary);
	}

	public function asString(string|null $variant = null): string|null
	{
		return $this->getFileForVariant($variant ?? 'original')?->asString();
	}

	// -----------------

	public function hasVariant(array|string $variant): bool
	{
		return Arr::containsAny($this->variants, is_array($variant) ? $variant : [$variant]);
	}

	public function publicUrl(string|null $variant = null): string
	{
		return $this->getPublicLocationForVariant($variant ?? 'original');
	}

	public function diskLocation(string|null $variant = null): string
	{
		return $this->getDiskLocationForVariant($variant ?? 'original');
	}

	// -----------------

	/**
	 * @return Bucket<int, MediaFolder>
	 */
	public function folders(): Bucket
	{
		if ($this->folder_id !== null) {
			return as_bucket($this->folder->parents())->set($this->folder_id, $this->folder);
		}

		return as_bucket([]);
	}

	public function foldersAsString(string $separator = ' / '): string
	{
		return implode($separator, $this->folders()->pluck('label')->toArray());
	}

	// -----------------

	public function getSrcset(): string|array
	{
		$variants = ['thumbs', 'small', 'medium', 'large'];
		$sizes = [', ', ' 1.5x, ', ' 2x, ', ' 2.5x, '];
		$result = '';
		$count = 0;

		foreach ($variants as $variant) {
			if (Arr::contains($this->variants, $variant) === false) {
				continue;
			}
			$result .= $this->getPublicLocationForVariant($variant).$sizes[$count];
			$count++;
		}

		if (empty($result) === false && $count < 4) {
			$result .= $this->getPublicLocationForVariant('original').$sizes[$count];
		}

		if (empty($result)) {
			return $this->getPublicLocationForVariant('original');
		}

		return trim($result, ', ');
	}

	// -----------------

	public function deleteFromDisk(string|null $variant = null): bool
	{
		try {
			$variants = is_string($variant) ? [$variant] : $this->variants;
			foreach ($variants as $variant) {
				$location = $this->getDiskLocationForVariant($variant);
				try {
					$this->disk->delete($location);
				} catch (UnableToDeleteFile) {
					continue;
				}
			}
		} catch (Throwable) {
			return false;
		}
		return true;
	}

	// -----------------

	protected function getFileForVariant(string $variant): File|null
	{
		if (isset($this->files[$variant])) {
			return $this->files[$variant];
		}

		$location = $this->getDiskLocationForVariant($variant);
		try {
			return $this->disk->file($location);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}

	protected function getPublicLocationForVariant(string $variant): string
	{
		return $this->disk->baseUrl().$this->getDiskLocationForVariant($variant);
	}

	protected function getDiskLocationForVariant(string $variant): string
	{
		$base = Str::finish($this->file_path, '/');
		if ($variant !== 'original' && in_array($variant, $this->variants)) {
			return $base.$variant.'/'.$this->file_name;
		}
		return $base.$this->file_name;
	}

}