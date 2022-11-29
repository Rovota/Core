<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Partials\Partial as PartialObject;
use Rovota\Core\Partials\PartialManager;

final class Partial
{

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Partials\Exceptions\MissingPartialException
	 */
	public static function make(string $name, string|null $source = null, array $variables = []): PartialObject
	{
		return PartialManager::make($name, $source, $variables);
	}

	public static function register(string $name, string $class): void
	{
		PartialManager::register($name, $class);
	}

	public static function isRegistered(string $name, string|null $class = null): bool
	{
		return PartialManager::isRegistered($name, $class);
	}

	// -----------------

	public static function hasVariable(string $partial, string $name): bool
	{
		return PartialManager::hasVariable($partial, $name);
	}

	public static function addVariable(array|string $partials, string $name, mixed $value): void
	{
		PartialManager::addVariable($partials, $name, $value);
	}

	public static function updateVariable(array|string $partials, string $name, mixed $value): void
	{
		PartialManager::updateVariable($partials, $name, $value);
	}

	public static function removeVariable(string $partial, string $name): void
	{
		PartialManager::removeVariable($partial, $name);
	}

}