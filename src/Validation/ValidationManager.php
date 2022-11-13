<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation;

use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Support\Collection;
use Rovota\Core\Support\Enums\Status;
use Throwable;

final class ValidationManager
{
	/**
	 * @var array<string, Validator>
	 */
	protected static array $validators = [];

	protected static string $default = 'default';

	protected static Collection $filters;

	protected static array $mime_types = [];
	protected static array $mime_types_reverse = [];

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
		self::addValidator('default', new Validator());

		try {
			self::$filters = Filter::where('status', Status::Enabled)->getBy('name');
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable, true);
		}
	}

	// -----------------

	public static function hasValidator(string $name): bool
	{
		return isset(self::$validators[$name]);
	}

	public static function addValidator(string $name, Validator $validator): void
	{
		if (self::hasValidator($name) === false) {
			self::$validators[$name] = $validator;
		}
	}

	public static function getValidator(string|null $name = null): Validator|null
	{
		if ($name === null) {
			$name = self::$default;
		}
		return self::$validators[$name] ?? null;
	}

	// -----------------

	public static function setDefault(string $name): void
	{
		if (self::hasValidator($name)) {
			self::$default = $name;
		}
	}

	// -----------------

	public static function hasFilter(string $name): bool
	{
		return self::$filters->has($name);
	}

	public static function getFilter(string $name): Filter|null
	{
		return self::$filters->get($name);
	}

	/**
	 * @return array<string, Filter>
	 */
	public static function getFilters(): array
	{
		return self::$filters->all();
	}

	// -----------------

	public static function mimeTypeExists(string $type): bool
	{
		self::loadMimeTypes();
		return isset(self::$mime_types[$type]);
	}

	public static function mimeTypeExtensions(string $type): array
	{
		self::loadMimeTypes();
		return self::$mime_types[$type] ?? [];
	}

	public static function extensionExists(string $extension): bool
	{
		self::loadMimeTypesReversed();
		return isset(self::$mime_types_reverse[$extension]);
	}

	public static function extensionMimeTypes(string $extension): array
	{
		self::loadMimeTypesReversed();
		return self::$mime_types_reverse[$extension] ?? [];
	}

	// -----------------

	protected static function loadMimeTypes(): void
	{
		if (empty(self::$mime_types)) {
			self::$mime_types = include 'mime_types.php';
		}
	}

	protected static function loadMimeTypesReversed(): void
	{
		if (empty(self::$mime_types_reverse)) {
			self::$mime_types_reverse = include 'mime_types_reverse.php';
		}
	}

}