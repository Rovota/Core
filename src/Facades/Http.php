<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Http\Client\Client;
use Rovota\Core\Http\Client\HibpClient;
use Rovota\Core\Http\Client\Request;
use Rovota\Core\Structures\Bucket;

final class Http
{

	protected function __construct()
	{
	}

	// -----------------

	public static function client(Bucket|array $config = []): Client
	{
		return new Client($config);
	}

	public static function haveIBeenPwnedClient(Bucket|array $config = []): HibpClient
	{
		return new HibpClient($config);
	}

	// -----------------

	public static function request(string $method, string $location, array $config = []): Request
	{
		return self::client()->request($method, $location, $config);
	}

	public static function get(string $location, array $config = []): Request
	{
		return self::client()->get($location, $config);
	}

	public static function delete(string $location, array $config = []): Request
	{
		return self::client()->delete($location, $config);
	}

	public static function head(string $location, array $config = []): Request
	{
		return self::client()->head($location, $config);
	}

	public static function options(string $location, array $config = []): Request
	{
		return self::client()->options($location, $config);
	}

	public static function patch(string $location, array $config = []): Request
	{
		return self::client()->patch($location, $config);
	}

	public static function post(string $location, array $config = []): Request
	{
		return self::client()->post($location, $config);
	}

	public static function put(string $location, array $config = []): Request
	{
		return self::client()->put($location, $config);
	}

}