<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Addon;

use Rovota\Core\Facades\DB;
use Rovota\Core\Kernel\Application;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Support\ArrOld;
use Rovota\Core\Support\Enums\Status;
use Throwable;

final class AddonManager
{
	/**
	 * @var array<string, Library>
	 */
	protected static array $libraries = [];

	/**
	 * @var array<string, Module>
	 */
	protected static array $modules = [];

	protected static Theme|null $theme = null;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 */
	public static function initialize(): void
	{
		self::loadLibraries();
		self::loadModules();
		self::loadTheme();

		foreach (self::$libraries as $library) {
			$library->eventAllAddonsLoaded();
		}
		foreach (self::$modules as $module) {
			$module->eventAllAddonsLoaded();
		}
		if (self::$theme instanceof Theme) {
			self::$theme->eventAllAddonsLoaded();
		}
	}

	// -----------------

	public static function getLibrary(string $name): Library|null
	{
		return self::$libraries[$name] ?? null;
	}

	public static function isLibraryEnabled(string $name): bool
	{
		return isset(self::$libraries[$name]);
	}

	/**
	 * @return \Rovota\Core\Addon\Library[]
	 */
	public static function getLibraries(): array
	{
		return self::$libraries;
	}

	// -----------------

	public static function getModule(string $name): Module|null
	{
		return self::$modules[$name] ?? null;
	}

	public static function isModuleEnabled(string $name): bool
	{
		return isset(self::$modules[$name]);
	}

	/**
	 * @return \Rovota\Core\Addon\Module[]
	 */
	public static function getModules(): array
	{
		return self::$modules;
	}

	// -----------------

	public static function getTheme(): Theme|null
	{
		return self::$theme ?? null;
	}

	public static function isThemeEnabled(): bool
	{
		return self::$theme instanceof Theme;
	}

	// -----------------

	protected static function loadLibraries(): void
	{
		try {
			$libraries = DB::table('addons')->where(['type' => 'library', 'status' => Status::Enabled])->get();
			foreach ($libraries as $library) {
				if ($library->domain_list === '*' || ArrOld::contains(explode(',', $library->domain_list), Application::$server->get('server_name'))) {
					$class = sprintf('Library\%s\%s', $library->name, $library->name);
					if (class_exists($class)) {
						$addon = $class::newFromBuilder($library);
						self::$libraries[$addon->name] = $addon;
					}
				}
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable, true);
		}
	}

	protected static function loadModules(): void
	{
		try {
			$modules = DB::table('addons')->where(['type' => 'module', 'status' => Status::Enabled])->get();
			foreach ($modules as $module) {
				if ($module->domain_list === '*' || ArrOld::contains(explode(',', $module->domain_list), Application::$server->get('server_name'))) {
					$class = sprintf('Module\%s\%s', $module->name, $module->name);
					if (class_exists($class)) {
						$addon = $class::newFromBuilder($module);
						self::$modules[$addon->name] = $addon;
					}
				}
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable, true);
		}
	}

	protected static function loadTheme(): void
	{
		try {
			$themes = DB::table('addons')->where(['type' => 'theme', 'status' => Status::Enabled])->get();
			foreach ($themes as $theme) {
				if ($theme->domain_list === '*' || ArrOld::contains(explode(',', $theme->domain_list), Application::$server->get('server_name'))) {
					$class = sprintf('Theme\%s\%s', $theme->name, $theme->name);
					if (class_exists($class)) {
						self::$theme = $class::newFromBuilder($theme);
					}
				}
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable, true);
		}
	}

}