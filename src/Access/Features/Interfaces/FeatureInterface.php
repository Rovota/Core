<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Access\Features\Interfaces;

use Rovota\Core\Access\Features\FeatureConfig;

interface FeatureInterface
{

	public function name(): string;

	public function config(): FeatureConfig;

	public function scope(): mixed;

	// -----------------

	public function active(): bool;

	public function value(): mixed;

	// -----------------

	public function withScope(mixed $scope): static;

}