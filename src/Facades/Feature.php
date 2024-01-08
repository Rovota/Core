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
		return FeatureManager::get($name);
	}

	public static function for(mixed $scope): Scope
	{
		return FeatureManager::getScope($scope);
	}

	// -----------------

	public static function active(string $name): bool
	{
		return FeatureManager::get($name)?->active() ?? false;
	}

	public static function value(string $name): mixed
	{
		return FeatureManager::get($name)?->value() ?? null;
	}

}