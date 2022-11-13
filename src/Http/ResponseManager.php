<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http;

use Rovota\Core\Http\Enums\StatusCode;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Support\Enums\Status;
use Rovota\Core\Support\Text;
use Throwable;

/**
 * @internal
 */
final class ResponseManager
{

	protected static array $headers;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		try {
			self::$headers = Header::where('status', Status::Enabled)->get()->pluck('value', 'name')->toArray();
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable, true);
		}
	}

	// -----------------

	public static function make(mixed $content, StatusCode $code): Response
	{
		return new Response(self::$headers, $content, $code);
	}

	public static function apiError(Throwable|ApiError|array $error, StatusCode $code): ApiErrorResponse
	{
		return new ApiErrorResponse(self::$headers, $error, $code);
	}

	public static function redirect(string|null $path, array $query, StatusCode $code): RedirectResponse
	{
		return new RedirectResponse(self::$headers, $path, $query, $code);
	}

	// -----------------

	public static function hasHeader(string $name): bool
	{
		return isset(self::$headers[$name]);
	}

	public static function hasHeaders(array $names): bool
	{
		foreach ($names as $name) {
			if (isset(self::$headers[$name]) === false) {
				return false;
			}
		}
		return true;
	}

	public static function addHeader(string $name, string $value): void
	{
		if (Text::length($name) > 0 && Text::length($value) > 0) {
			self::$headers[$name] = $value;
		}
	}

	public static function addHeaders(array $headers): void
	{
		foreach ($headers as $name => $value) {
			self::addHeader($name, $value);
		}
	}

	public static function removeHeader(string $name): void
	{
		unset(self::$headers[$name]);
	}

	public static function removeHeaders(array $headers): void
	{
		foreach ($headers as $name) {
			unset(self::$headers[$name]);
		}
	}

}