<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Localization;

use Rovota\Core\Database\Model;
use Rovota\Core\Support\Enums\Status;
use Rovota\Core\Support\Moment;

/**
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string $label_native
 * @property string $locale
 * @property Status $status
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class Language extends Model
{

	protected string|null $table = 'languages';

	protected array $attributes = [
		'status' => Status::Disabled,
	];

	protected array $casts = [
		'status' => ['enum', Status::class],
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	// -----------------

	public function format(string $key, mixed $default = null): mixed
	{
		return LocalizationManager::getFormatsByLocale($this->locale)->get($key, $default);
	}

	// -----------------

	public function getOptionHtml(Language|string|null $active = null): string
	{
		if ($active instanceof Language) {
			$active = $active->locale;
		}
		return sprintf('<option value="%s"%s>%s</option>', $this->locale, ($this->locale === $active) ? ' selected' : '', $this->label_native);
	}

}