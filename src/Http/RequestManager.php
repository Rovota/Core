<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http;

use Rovota\Core\Session\SessionManager;
use Rovota\Core\Support\Str;

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
		$body = self::getRequestBody();
		$post = self::getRequestPostData();
		$query = self::getRequestQueryData();
		$headers = self::getRequestHeaders();

		self::$request = new Request($body, $post, $query, $headers);

		$continue = self::$request->query->string('continue');
		if (mb_strlen($continue) > 0) {
			SessionManager::get()->put('location.continue', $continue);
		}
	}

	// -----------------

	public static function getRequest(): Request
	{
		return self::$request;
	}

	// -----------------

	protected static function getRequestBody(): string|null
	{
		$body = file_get_contents('php://input');
		return $body === false ? null : trim($body);
	}

	protected static function getRequestPostData(): array
	{
		$data = $_POST;
		array_walk_recursive($data, function(&$item) {
			if (is_string($item)) {
				$item = mb_strlen(trim($item)) > 0 ? trim($item) : null;
			}
		});

		$files = FilesArrayOrganizer::organize($_FILES);

		return array_merge($data, $files);
	}

	protected static function getRequestQueryData(): array
	{
		parse_str(Str::after($_SERVER['REQUEST_URI'], '?'), $parameters);
		return self::filterParameters($parameters);
	}

	protected static function getRequestHeaders(): array
	{
		return array_change_key_case(getallheaders());
	}

	// -----------------

	protected static function filterParameters(array $parameters): array
	{
		foreach ($parameters as $key => $value) {
			if (is_array($value)) {
				$parameters[$key] = self::filterParameters($value);
			} else {
				if (mb_strlen(trim($value)) > 0) {
					$parameters[$key] = $value;
				}
			}
		}

		return $parameters;
	}

}