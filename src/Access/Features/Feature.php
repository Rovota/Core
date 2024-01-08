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

abstract class Feature implements FeatureInterface
{

	protected string $name;

	protected FeatureConfig $config;

	protected Scope $scope;

	// -----------------

	public function __construct(string $name, FeatureConfig $config)
	{
		$this->name = trim($name);
		$this->config = $config;

		$this->scope = FeatureManager::getScope();
	}

	// -----------------

	public function name(): string
	{
		return $this->name;
	}

	public function config(): FeatureConfig
	{
		return $this->config;
	}

	public function scope(): Scope
	{
		return $this->scope;
	}

	// -----------------

	public function active(): bool
	{
		$result = $this->value();

		return match(true) {
			is_bool($result) => $result,
			is_string($result) => true,
			default => false
		};
	}

	public function value(mixed $default = null): mixed
	{
		if ($this->scope->getCache()->missing($this->name)) {
			$this->scope->getCache()->set($this->name, $this->resolve());
		}

		return $this->scope->getCache()->get($this->name, $default);
	}

	// -----------------

	/**
	 * @internal
	 */
	public function withScope(mixed $scope): static
	{
		$this->scope = $scope;
		return $this;
	}

	// -----------------

	protected function resolve(): mixed
	{
		return false;
	}

}