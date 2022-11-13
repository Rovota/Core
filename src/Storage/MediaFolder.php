<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage;

use Rovota\Core\Database\Model;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Moment;
use Throwable;

/**
 * @property int $id
 * @property int|null $parent_id
 * @property string $name
 * @property string $label
 * @property bool $managed
 * @property bool $pinned
 * @property int $owner_id
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 *
 * @property-read MediaFolder|null $parent
 */
class MediaFolder extends Model
{

	protected string|null $table = 'media_folders';

	protected array $attributes = [
		'managed' => false,
		'pinned' => false,
	];

	protected array $casts = [
		'managed' => 'bool',
		'pinned' => 'bool',
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	// -----------------

	public function getParentAttribute(): MediaFolder|null
	{
		return MediaManager::getOrLoadFolder($this->parent_id);
	}

	public function setParentAttribute(MediaFolder|int|null $folder): void
	{
		if ($folder instanceof MediaFolder) {
			$this->parent_id = $folder->id;
		} else {
			if ($folder === null) {
				$this->parent_id = null;
			} else {
				$this->parent_id = $folder;
			}
		}
	}

	// -----------------

	/**
	 * @return Bucket<int, MediaFolder>
	 */
	public function parents(): Bucket
	{
		if ($this->parent_id !== null) {
			return as_bucket($this->parent->parents())->set($this->parent_id, $this->parent);
		}

		return as_bucket([]);
	}

	// -----------------

	/**
	 * @return Bucket<int, Media>
	 */
	public function media(): Bucket
	{
		try {
			return Media::where(['folder_id' => $this->id])->getBy('id');
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
			return new Bucket();
		}
	}

	/**
	 * @return Bucket<int, MediaFolder>
	 */
	public function folders(): Bucket
	{
		try {
			return MediaFolder::where(['parent_id' => $this->id])->getBy('id');
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
			return new Bucket();
		}
	}

}