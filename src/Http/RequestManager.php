<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http;

/**
 * @internal
 */
final class RequestManager
{

	// -----------------

	public static function initialize(): void
	{
		$post = self::getRequestPostData();
	}

	// -----------------

	protected static function getRequestPostData(): array
	{
		$data = $_POST;

		$files = FilesArrayOrganizer::organize($_FILES);

		return array_merge($data, $files);
	}

}