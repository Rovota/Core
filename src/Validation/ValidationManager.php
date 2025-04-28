<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Validation;

use Rovota\Core\Validation\Exceptions\MissingValidatorException;
use Rovota\Core\Validation\Interfaces\ValidatorInterface;

final class ValidationManager
{
	/**
	 * @var array<string, ValidatorInterface>
	 */
	protected static array $validators = [];

	protected static string|null $default = null;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		self::registerDefaultValidators();
		self::setDefault('default');

		RuleManager::initialize();
		FilterManager::initialize();
	}

	// -----------------

	public static function register(string $name, Validator $validator): void
	{
		if (isset(self::$validators[$name]) === false) {
			self::$validators[$name] = $validator;
		}
	}

	// -----------------

	public static function has(string $name): bool
	{
		return isset(self::$validators[$name]);
	}

	public static function get(string|null $name = null): ValidatorInterface|null
	{
		if ($name === null) {
			$name = self::$default;
		}
		if (!isset(self::$validators[$name])) {
			ExceptionHandler::addThrowable(new MissingValidatorException("There is no validator registered with the name '$name'."));
		}
		return self::$validators[$name] ?? null;
	}

	/**
	 * @returns array<string, ValidatorInterface>
	 */
	public static function all(): array
	{
		return self::$validators;
	}

	// -----------------

	public static function getDefault(): string|null
	{
		return self::$default;
	}

	public static function setDefault(string $name): void
	{
		if (isset(self::$validators[$name]) === false) {
			ExceptionHandler::addThrowable(new MissingValidatorException("Undefined validators cannot be set as default: '$name'."));
		}
		self::$default = $name;
	}

	// -----------------

	protected static function registerDefaultValidators(): void
	{
		self::register('default', new Validator([], []));
	}


}