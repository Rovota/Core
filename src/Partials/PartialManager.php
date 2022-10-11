<?php
/** @noinspection DuplicatedCode */

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Partials;

use Rovota\Core\Addon\AddonManager;
use Rovota\Core\Partials\Exceptions\MissingPartialException;

/**
 * @internal
 */
final class PartialManager
{
	/**
	 * @var array<string, string>
	 */
	protected static array $partials = [];

	/**
	 * @var array<string, array<string, mixed>>
	 */
	protected static array $variables = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Partials\Exceptions\MissingPartialException
	 */
	public static function make(string $name, string|null $source, array $variables = []): Partial
	{
		if (isset(self::$partials[$name]) && $source === null) {
			$name = self::$partials[$name];
		}

		if (str_contains($name, '\\')) {
			return new $name(null, [
				'variables' => array_merge(self::$variables['*'] ?? [], self::$variables[$name] ?? [], $variables),
			]);
		}

		$layout = self::getMatchingLayout($name, $source);
		return new Partial($layout, [
			'variables' => array_merge(self::$variables['*'] ?? [], self::$variables[$name] ?? [], $variables),
		]);
	}

	public static function register(string $name, string $class): void
	{
		self::$partials[$name] = $class;
	}

	public static function isRegistered(string $name, string|null $class = null): bool
	{
		if (isset(self::$partials[$name]) === false) {
			return false;
		}

		if ($class !== null && self::$partials[$name] !== $class) {
			return false;
		}

		return true;
	}

	// -----------------

	public static function hasVariable(string $partial, string $name): bool
	{
		return isset(self::$variables[$partial][$name]);
	}

	public static function addVariable(array|string $partials, string $name, mixed $value): void
	{
		$partials = is_array($partials) ? $partials : [$partials];
		foreach ($partials as $partial) {
			if (isset(self::$partials[$partial])) {
				$partial = self::$partials[$partial];
			}
			self::$variables[$partial][$name] = $value;
		}
	}

	public static function updateVariable(array|string $partials, string $name, mixed $value): void
	{
		$partials = is_array($partials) ? $partials : [$partials];
		foreach ($partials as $partial) {
			if (isset(self::$partials[$partial])) {
				$partial = self::$partials[$partial];
			}
			if (is_array($value)) {
				foreach ($value as $key => $item) {
					self::$variables[$partial][$name][$key] = $item;
				}
			} else {
				self::$variables[$partial][$name] = $value;
			}
		}
	}

	public static function addOrUpdateVariable(array|string $partials, string $name, mixed $value): void
	{
		$partials = is_array($partials) ? $partials : [$partials];
		foreach ($partials as $partial) {
			if (self::hasVariable($partial, $name)) {
				self::updateVariable($partial, $name, $value);
			} else {
				self::addVariable($partial, $name, $value);
			}
		}
	}

	public static function removeVariable(string $partial, string $name): void
	{
		unset(self::$variables[$partial][$name]);
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Partials\Exceptions\MissingPartialException
	 */
	protected static function getMatchingLayout(string $name, string|null $source = null): string
	{
		$path = sprintf('/templates/partials/%s.php', $name);

		if ($source !== null) {
			if (file_exists(base_path($source).$path)) {
				return $source.$path;
			} else {
				throw new MissingPartialException("Partial could not be found: $source$path.");
			}
		} else {
			if (AddonManager::isThemeEnabled() === true) {
				$source = '/themes/'.AddonManager::getTheme()->name;
				if (file_exists(base_path($source).$path)) {
					return $source.$path;
				}
			}

			// Attempt to use a fallback when above fails.
			$source = '/vendor/rovota/core/src/Web';
			if (file_exists(base_path($source).$path)) {
				return $source.$path;
			} else {
				throw new MissingPartialException("Partial could not be found. Fallback not available for: $name.");
			}
		}
	}

}