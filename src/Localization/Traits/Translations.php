<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Localization\Traits;

use Rovota\Core\Database\Model;
use Rovota\Core\Facades\Registry;
use Rovota\Core\Localization\LocalizationManager;
use Rovota\Core\Localization\Translation;
use Rovota\Core\Support\Enums\PostStatus;

trait Translations
{

	public Translation|null $translation = null;

	// -----------------

	public function hasPreferredLanguage(): bool
	{
		return $this->translation->language_id === LocalizationManager::getActiveLanguage()->id;
	}

	// -----------------

	protected function loadTranslation(): void
	{
		/**
		 * @var Model $this
		 */
		$active_language_id = LocalizationManager::getActiveLanguage()->id;
		$result = $this->{'i18n_model'}::where([$this->{'i18n_foreign_key'} => $this->getId(), 'language_id' => $active_language_id, 'status' => PostStatus::Visible])->first();

		if ($result instanceof Translation) {
			$this->translation = $result;
			return;
		}

		$registry_language_id = LocalizationManager::getLanguage(Registry::string('default_locale', 'en_US'))->id;
		$result = $this->{'i18n_model'}::where([$this->{'i18n_foreign_key'} => $this->getId(), 'language_id' => $registry_language_id, 'status' => PostStatus::Visible])->first();

		if ($result instanceof Translation) {
			$this->translation = $result;
		}
	}

}