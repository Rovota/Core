<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Addon\AddonManager;
use Rovota\Core\Addon\Theme as ThemeModel;

final class Theme
{

	protected function __construct()
	{
	}

	// -----------------

	public static function get(): ThemeModel
	{
		return AddonManager::getTheme();
	}

	public static function enabled(): bool
	{
		return AddonManager::isThemeEnabled();
	}

}