<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Localization;

use Rovota\Core\Database\Model;

class Language extends Model
{

	public function getOptionHtml(Language|string|null $active = null): string
	{
		if ($active instanceof Language) {
			$active = $active->locale;
		}
		return sprintf('<option value="%s"%s>%s</option>', $this->locale, ($this->locale === $active) ? ' selected' : '', $this->label_native);
	}

}