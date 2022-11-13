<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Addon\AddonManager;
use Rovota\Core\Addon\Library as LibraryModel;

final class Library
{

	protected function __construct()
	{
	}

	// -----------------

	public static function get(string $name): LibraryModel
	{
		return AddonManager::getLibrary($name);
	}

	public static function enabled(string $name): bool
	{
		return AddonManager::isLibraryEnabled($name);
	}

	/**
	 * @returns array<string, LibraryModel>
	 */
	public static function all(): array
	{
		return AddonManager::getLibraries();
	}

}