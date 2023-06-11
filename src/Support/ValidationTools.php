<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support;

use Rovota\Core\Database\ConnectionManager;
use Rovota\Core\Facades\DB;
use Rovota\Core\Http\UploadedFile;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Storage\Interfaces\FileInterface;
use Throwable;

final class ValidationTools
{

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
	public static function processDatabaseOptions(string $field, array $options): array
	{
		$config = [
			'connection' => ConnectionManager::getDefault(),
			'column' => $field,
			'model' => null,
		];

		if (str_contains($options[0],'\\')) {
			$model = new $options[0]();
			$config['connection'] = $model->getConnection();
			$config['table'] = $model->getTable();
			$config['model'] = $model::class;
		} else if (str_contains($options[0],'.')) {
			$location = explode('.', $options[0]);
			$config['connection'] = $location[0];
			$config['table'] = $location[1];
		} else {
			$config['table'] = $options[0];
		}

		if (isset($options[1]) && is_string($options[1])) {
			$config['column'] = $options[1];
		}

		return $config;
	}

	/**
	 * @internal
	 */
	public static function getOccurrences(array $config, mixed $data): int
	{
		try {
			if ($config['model'] === null) {
				$occurrences = DB::connection($config['connection'])->table($config['table'])->where($config['column'], $data)->count();
			} else {
				$occurrences = $config['model']::where($config['column'], $data)->count();
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
			return 0;
		}

		return $occurrences ?? 0;
	}

	// -----------------

	public static function getSize(mixed $data): int|float
	{
		return match(true) {
			$data instanceof FileInterface => round($data->properties()->size / 1024), // Bytes to Kilobytes
			$data instanceof UploadedFile => round($data->variant('original')->properties()->size / 1024), // Bytes to Kilobytes
			is_int($data), is_float($data) => $data,
			is_numeric($data), is_string($data) => Str::length($data),
			is_array($data) => count($data),
			default => 0
		};
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