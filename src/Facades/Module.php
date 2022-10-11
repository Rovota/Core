<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Addon\AddonManager;
use Rovota\Core\Addon\Module as ModuleModel;

final class Module
{

	protected function __construct()
	{
	}

	// -----------------

	public static function get(string $name): ModuleModel
	{
		return AddonManager::getModule($name);
	}

	public static function enabled(string $name): bool
	{
		return AddonManager::isModuleEnabled($name);
	}

	/**
	 * @returns array<string, ModuleModel>
	 */
	public static function all(): array
	{
		return AddonManager::getModules();
	}

}