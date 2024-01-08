<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Closure;
use Rovota\Core\Access\Features\FeatureManager;
use Rovota\Core\Access\Features\Interfaces\FeatureInterface;
use Rovota\Core\Access\Features\Scope;

final class Feature
{

	protected function __construct()
	{
	}

	// -----------------

	public static function define(string $name, Closure|bool|string $callback): void
	{
		FeatureManager::register($name, $callback);
	}

	// -----------------

	public static function get(string $name): FeatureInterface|null
	{
		return FeatureManager::getScope()->get($name);
	}

	public static function for(mixed $scope): Scope
	{
		return FeatureManager::getScope($scope);
	}

	// -----------------

	public static function active(string $name): bool
	{
		return self::get($name)?->active() ?? false;
	}

	public static function value(string $name, mixed $default = null): mixed
	{
		return self::get($name)?->value($default) ?? $default;
	}

	// -----------------

	public static function forget(string $name): void
	{
		FeatureManager::getScope()->forget($name);
	}

	public static function update(string $name, mixed $value): void
	{
		FeatureManager::getScope()->update($name, $value);
	}

}