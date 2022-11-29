<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage;

use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Storage\Enums\MediaType;
use Rovota\Core\Support\Str;
use Throwable;

final class MediaManager
{
	/**
	 * @var array<int, MediaFolder>
	 */
	protected static array $folders = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------
	// Folders

	public static function loadFolder(int $identifier): void
	{
		try {
			$folder = MediaFolder::find($identifier);
			if ($folder instanceof MediaFolder) {
				self::$folders[$folder->id] = $folder;
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable, true);
		}
	}

	public static function rememberFolder(MediaFolder $folder): void
	{
		self::$folders[$folder->id] = $folder;
	}

	public static function knowsFolder(int $identifier): bool
	{
		return isset(self::$folders[$identifier]);
	}

	public static function getFolder(int $identifier): MediaFolder|null
	{
		return self::$folders[$identifier] ?? null;
	}

	public static function getOrLoadFolder(int $identifier): MediaFolder|null
	{
		if (self::knowsFolder($identifier) === false) {
			self::loadFolder($identifier);
		}
		return self::$folders[$identifier] ?? null;
	}

	/**
	 * @returns array<int, MediaFolder>
	 */
	public static function getFolders(): array
	{
		return self::$folders;
	}

	// -----------------
	// Misc

	public static function getMediaType(string $mime_type): MediaType|null
	{
		return match(true) {
			Str::containsAny($mime_type, ['officedocument', 'pdf', 'ms-']) => MediaType::Document,
			Str::containsAny($mime_type, ['image', 'pdf']) => MediaType::Image,
			Str::containsAny($mime_type, ['video']) => MediaType::Video,
			Str::containsAny($mime_type, ['audio']) => MediaType::Audio,
			Str::containsAny($mime_type, ['font']) => MediaType::Font,
			Str::containsAny($mime_type, ['gzip', 'zip', 'compressed']) => MediaType::Archive,
			default => MediaType::Unknown,
		};
	}

}