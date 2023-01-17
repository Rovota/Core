<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database;

use Rovota\Core\Database\Casts\ArrayCast;
use Rovota\Core\Database\Casts\BooleanCast;
use Rovota\Core\Database\Casts\BucketCast;
use Rovota\Core\Database\Casts\Cast;
use Rovota\Core\Database\Casts\DateTimeCast;
use Rovota\Core\Database\Casts\EncryptionCast;
use Rovota\Core\Database\Casts\EncryptionStringCast;
use Rovota\Core\Database\Casts\EnumCast;
use Rovota\Core\Database\Casts\FloatCast;
use Rovota\Core\Database\Casts\TextCast;
use Rovota\Core\Database\Casts\IntegerCast;
use Rovota\Core\Database\Casts\JsonCast;
use Rovota\Core\Database\Casts\MapCast;
use Rovota\Core\Database\Casts\ModelCast;
use Rovota\Core\Database\Casts\MomentCast;
use Rovota\Core\Database\Casts\ObjectCast;
use Rovota\Core\Database\Casts\SequenceCast;
use Rovota\Core\Database\Casts\SerialCast;
use Rovota\Core\Database\Casts\SetCast;
use Rovota\Core\Database\Casts\StringCast;

final class CastManager
{
	/**
	 * @var array<string, Cast>
	 */
	protected static array $casts = [];

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
		self::registerDefaultCasts();
	}

	// -----------------

	public static function castToRaw(mixed $value, string|array $options): mixed
	{
		if (is_string($options)) {
			$options = [$options];
		}

		$cast = self::findCast(array_shift($options));
		if ($cast !== null) {
			return $cast->set($value, $options);
		}

		return $value;
	}

	public static function castFromRaw(mixed $value, string|array $options): mixed
	{
		if (is_string($options)) {
			$options = [$options];
		}

		$cast = self::findCast(array_shift($options));
		if ($cast !== null) {
			return $cast->get($value, $options);
		}

		return $value;
	}

	// -----------------

	public static function castToRawAutomatic(mixed $value): mixed
	{
		foreach (self::$casts as $cast) {
			if ($cast->supportsValue($value)) {
				return $cast->set($value, []);
			}
		}

		return $value;
	}

	// -----------------

	public static function register(string $name, Cast $cast): void
	{
		self::$casts[$name] = $cast;
	}

	// -----------------

	public static function isAllowedValueForCast(array|string $options, mixed $value): bool
	{
		if (is_string($options)) {
			$options = [$options];
		}

		$name = array_shift($options);
		if (isset(self::$casts[$name]) === false) {
			return false;
		}

		return self::$casts[$name]->allowedValue($value, $options);
	}

	// -----------------

	protected static function findCast(string $name): Cast|null
	{
		return self::$casts[$name] ?? null;
	}

	// -----------------

	protected static function registerDefaultCasts(): void
	{
		self::register('array', new ArrayCast());
		self::register('bool', new BooleanCast());
		self::register('bucket', new BucketCast());
		self::register('datetime', new DateTimeCast());
		self::register('encrypted', new EncryptionCast());
		self::register('encrypted_string', new EncryptionStringCast());
		self::register('enum', new EnumCast());
		self::register('float', new FloatCast());
		self::register('text', new TextCast());
		self::register('int', new IntegerCast());
		self::register('json', new JsonCast());
		self::register('map', new MapCast());
		self::register('model', new ModelCast());
		self::register('moment', new MomentCast());
		self::register('sequence', new SequenceCast());
		self::register('serial', new SerialCast());
		self::register('set', new SetCast());
		self::register('string', new StringCast());
		self::register('object', new ObjectCast());
	}

}