<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Access\Features;

use Closure;
use Envms\FluentPDO\Exception;
use Rovota\Core\Access\Features\Drivers\Local;
use Rovota\Core\Access\Features\Drivers\Remote;
use Rovota\Core\Access\Features\Enums\Driver;
use Rovota\Core\Access\Features\Interfaces\FeatureInterface;
use Rovota\Core\Database\ConnectionManager;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Str;

/**
 * @internal
 */
final class FeatureManager
{
	/**
	 * @var array<string, Feature>
	 */
	protected static array $features = [];

	/**
	 * @var array>string, bool>
	 */
	protected static Bucket $cache;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @throws Exception
	 */
	public static function initialize(): void
	{
		self::$cache = new Bucket();

		self::registerRemoteFeatures();
	}

	// -----------------

	public static function register(string $name, Closure|bool|string $callback): void
	{
		$feature = self::build($name, [
			'driver' => 'local',
			'label' => Str::title($name),
			'callback' => $callback,
		]);

		if ($feature !== null) {
			self::$features[$feature->name()] = $feature;
		}
	}

	public static function build(string $name, array $config): FeatureInterface|null
	{
		$config = new FeatureConfig($config);

		return match($config->driver) {
			Driver::Local => new Local($name, $config),
			Driver::Remote => new Remote($name, $config),
			default => null,
		};
	}

	// -----------------

	public static function get(string $name): FeatureInterface|null
	{
		return self::$features[$name];
	}

	/**
	 * @returns array<string, FeatureInterface>
	 */
	public static function all(): array
	{
		return self::$features;
	}

	// -----------------

	public static function cache(): Bucket
	{
		return self::$cache;
	}

	// -----------------

	public static function getScope(mixed $scope): Scope
	{
		return new Scope($scope);
	}

	// -----------------

	/**
	 * @throws Exception
	 */
	protected static function registerRemoteFeatures(): void
	{
		if (ConnectionManager::get()->hasTable('core_features')) {
			/** @var DatabaseEntry $feature */
			foreach (DatabaseEntry::all() as $feature) {

				$feature = self::build($feature->name, [
					'driver' => 'remote',
					'label' => $feature->label,
					'description' => $feature->description,
					'variant' => $feature->variant,
					'enabled' => $feature->enabled,
				]);

				if ($feature !== null) {
					self::$features[$feature->name()] = $feature;
				}
			}
		}
	}

}