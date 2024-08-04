<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http;

use Rovota\Core\Session\SessionManager;

/**
 * @internal
 */
final class RequestManager
{

	protected static Request $request;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		$post = self::getRequestPostData();

		$continue = self::$request->query->string('continue');
		if (mb_strlen($continue) > 0) {
			SessionManager::get()->put('location.next', $continue);
		}
	}

	// -----------------

	protected static function getRequestPostData(): array
	{
		$data = $_POST;

		$files = FilesArrayOrganizer::organize($_FILES);

		return array_merge($data, $files);
	}

}