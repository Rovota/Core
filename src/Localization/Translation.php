<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Localization;

use Rovota\Core\Database\Model;
use Rovota\Core\Support\Enums\PostStatus;
use Rovota\Core\Support\Moment;

/**
 * @property int $id
 * @property int $language_id
 * @property PostStatus $status
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
abstract class Translation extends Model
{

	protected array $attributes = [
		'status' => PostStatus::Draft,
	];

	protected array $casts = [
		'status' => ['enum', PostStatus::class],
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	// -----------------

	public Language $language;

	// -----------------

	public function eventModelLoaded(): void
	{
		$this->language = LocalizationManager::getLanguage($this->language_id);
	}

}