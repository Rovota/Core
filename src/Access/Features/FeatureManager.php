<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Access\Features;

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

	protected static Bucket $scopes;

	/**
	 * @var array<string, Feature>
	 */
	protected static array $features = [];

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
		self::$scopes = new Bucket();

		self::registerDefaultScope();
		self::registerRemoteFeatures();
	}

	// -----------------

	public static function register(string $name, mixed $definition): void
	{
		$feature = self::build($name, [
			'driver' => 'local',
			'label' => Str::title($name),
			'definition' => $definition,
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
		return self::$features[$name] ?? null;
	}

	public static function getScope(mixed $scope = null): Scope
	{
		if ($scope === null) {
			$scope = 'global';
		}

		if (self::$scopes->missing($scope)) {
			self::$scopes->set($scope, new Scope($scope));
		}

		return self::$scopes->get($scope);
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

	protected static function registerDefaultScope(): void
	{
		self::$scopes['global'] = new Scope();
	}

}