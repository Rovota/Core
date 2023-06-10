<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Views\Components\Meta;
use Rovota\Core\Views\Components\Script;
use Rovota\Core\Views\Components\Style;
use Rovota\Core\Views\Exceptions\MissingViewException;
use Rovota\Core\Views\View as ViewObject;
use Rovota\Core\Views\ViewManager;

final class View
{

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @throws MissingViewException
	 */
	public static function make(string $name, string|null $source = null): ViewObject
	{
		return ViewManager::make($name, $source);
	}

	public static function register(string $name, string $class): void
	{
		ViewManager::register($name, $class);
	}

	public static function isRegistered(string $name, string|null $class = null): bool
	{
		return ViewManager::isRegistered($name, $class);
	}

	// -----------------

	public static function hasStyle(string $view, string $identifier): bool
	{
		return ViewManager::hasStyle($view, $identifier);
	}

	public static function addStyle(array|string $views, string $identifier, Style|array $attributes): Style
	{
		return ViewManager::addStyle($views, $identifier, $attributes);
	}

	public static function updateStyle(array|string $views, string $identifier, array $attributes): void
	{
		ViewManager::updateStyle($views, $identifier, $attributes);
	}

	public static function removeStyle(string $view, string $identifier): void
	{
		ViewManager::removeStyle($view, $identifier);
	}

	// -----------------

	public static function hasScript(string $view, string $identifier): bool
	{
		return ViewManager::hasScript($view, $identifier);
	}

	public static function addScript(array|string $views, string $identifier, Script|array $attributes): Script
	{
		return ViewManager::addScript($views, $identifier, $attributes);
	}

	public static function updateScript(array|string $views, string $identifier, array $attributes): void
	{
		ViewManager::updateScript($views, $identifier, $attributes);
	}

	public static function removeScript(string $view, string $identifier): void
	{
		ViewManager::removeScript($view, $identifier);
	}

	// -----------------

	public static function hasVariable(string $view, string $name): bool
	{
		return ViewManager::hasVariable($view, $name);
	}

	public static function addVariable(array|string $views, string $name, mixed $value): void
	{
		ViewManager::addVariable($views, $name, $value);
	}

	public static function updateVariable(array|string $views, string $name, mixed $value): void
	{
		ViewManager::updateVariable($views, $name, $value);
	}

	public static function removeVariable(string $view, string $name): void
	{
		ViewManager::removeVariable($view, $name);
	}

	// -----------------

	public static function hasMeta(string $view, string $identifier): bool
	{
		return ViewManager::hasMeta($view, $identifier);
	}

	public static function addMeta(array|string $views, string $identifier, Meta|array $attributes): Meta
	{
		return ViewManager::addMeta($views, $identifier, $attributes);
	}

	public static function updateMeta(array|string $views, string $identifier, array $attributes): void
	{
		ViewManager::updateMeta($views, $identifier, $attributes);
	}

	public static function removeMeta(string $view, string $identifier): void
	{
		ViewManager::removeMeta($view, $identifier);
	}

}