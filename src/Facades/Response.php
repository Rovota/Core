<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Facades;

use Exception;
use Rovota\Core\Http\ApiErrorResponse;
use Rovota\Core\Http\Enums\StatusCode;
use Rovota\Core\Http\RedirectResponse;
use Rovota\Core\Http\Response as ResponseObject;
use Rovota\Core\Http\ResponseManager;

final class Response
{

	protected function __construct()
	{
	}

	// -----------------

	public static function make(mixed $content, StatusCode $code = StatusCode::Ok): ResponseObject
	{
		return ResponseManager::make($content, $code);
	}

	public static function apiError(Exception|array $error, StatusCode $code = StatusCode::BadRequest): ApiErrorResponse
	{
		return ResponseManager::apiError($error, $code);
	}

	public static function redirect(string|null $path = null, array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		return ResponseManager::redirect($path, $query, $code);
	}

	public static function away(string $location, array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		return ResponseManager::redirect(null, [], $code)->away($location, $query, $code);
	}

	public static function intended(string $default = '/', array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		return ResponseManager::redirect(null, [], $code)->intended($default, $query, $code);
	}

	public static function continue(array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		return ResponseManager::redirect(null, [], $code)->continue($query, $code);
	}

	public static function route(string $name, array $params = [], array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		return ResponseManager::redirect(null, [], $code)->route($name, $params, $query, $code);
	}

	// -----------------

	public static function hasHeader(string $name): bool
	{
		return ResponseManager::hasHeader($name);
	}

	public static function hasHeaders(array $names): bool
	{
		return ResponseManager::hasHeaders($names);
	}

	public static function addHeader(string $name, string $value): void
	{
		ResponseManager::addHeader($name, $value);
	}

	public static function addHeaders(array $headers): void
	{
		ResponseManager::addHeaders($headers);
	}

	public static function removeHeader(string $name): void
	{
		ResponseManager::removeHeader($name);
	}

	public static function removeHeaders(array $headers): void
	{
		ResponseManager::removeHeaders($headers);
	}

}