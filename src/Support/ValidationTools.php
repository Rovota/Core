<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support;

use Rovota\Core\Http\UploadedFile;
use Rovota\Core\Storage\Interfaces\FileInterface;

final class ValidationTools
{

	protected function __construct()
	{
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

}