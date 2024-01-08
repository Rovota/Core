<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 *
 * Inspired by Laravel rate limiting.
 */

namespace Rovota\Core\Access\Features;

use Rovota\Core\Access\Features\Interfaces\FeatureInterface;

final class Scope
{

	protected mixed $scope = null;

	// -----------------

	public function __construct(mixed $scope)
	{
		$this->scope = $scope;
	}

	// -----------------

	public function get(string $name): FeatureInterface|null
	{
		return FeatureManager::get($name)?->withScope($this->scope);
	}

	// -----------------

	public function active(string $name): bool
	{
		return FeatureManager::get($name)?->withScope($this->scope)->active();
	}

	public function value(string $name): mixed
	{
		return FeatureManager::get($name)?->withScope($this->scope)->value();
	}

}