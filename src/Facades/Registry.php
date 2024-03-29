<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use BackedEnum;
use Rovota\Core\Kernel\Application;
use Rovota\Core\Kernel\RegistryEntry;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Text;

final class Registry
{

	protected function __construct()
	{
	}

	// -----------------

	public static function array(string $name, array $default = []): array
	{
		return Application::$registry->array($name, $default);
	}

	public static function bool(string $name, bool $default = false): bool
	{
		return Application::$registry->bool($name, $default);
	}

	public static function float(string $name, float $default = 0.00): float
	{
		return Application::$registry->float($name, $default);
	}

	public static function int(string $name, int $default = 0): int
	{
		return Application::$registry->int($name, $default);
	}

	public static function enum(string $name, string $class, BackedEnum|null $default = null): BackedEnum|null
	{
		return Application::$registry->enum($name, $class, $default);
	}

	public static function moment(string $name, Moment $default = new Moment()): Moment
	{
		return Application::$registry->moment($name, $default);
	}

	public static function string(string $name, string $default = ''): string
	{
		return Application::$registry->string($name, $default);
	}

	public static function text(string $name, Text|string $default = ''): Text
	{
		return Application::$registry->text($name, $default);
	}

	// -----------------

	public static function has(string $name): bool
	{
		return Application::$registry->has($name);
	}

	public static function get(string $name): RegistryEntry|null
	{
		return Application::$registry->get($name);
	}

	public static function all(string|null $vendor = null): array
	{
		return Application::$registry->all($vendor);
	}

	public static function temporary(string $name, mixed $value): void
	{
		Application::$registry->temporary($name, $value);
	}

	public static function save(string $name, mixed $value): bool
	{
		return Application::$registry->save($name, $value);
	}

	public static function delete(string $name, bool $permanent = false): bool
	{
		return Application::$registry->delete($name, $permanent);
	}

}