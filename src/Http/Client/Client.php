<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http\Client;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\RedirectMiddleware;
use Rovota\Core\Http\Client\Traits\ConfigModifiers;
use Rovota\Core\Http\Client\Traits\ClientMethods;
use Rovota\Core\Kernel\Application;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Traits\Conditionable;

class Client
{
	use ConfigModifiers, ClientMethods, Conditionable;

	// -----------------

	protected Guzzle|null $guzzle = null;

	protected Bucket $config;

	// -----------------

	public function __construct(Bucket|array $config = [])
	{
		$this->config = is_array($config) ? new Bucket($config) : $config;
		$this->setClientDefaults();
	}

	// -----------------

	protected function setClientDefaults(): void
	{
		$this->config->merge([
			'allow_redirects' => RedirectMiddleware::$defaultSettings,
			'http_errors' => false,
			'decode_content' => true,
			'verify' => true,
			'cookies' => false,
			'idn_conversion' => false,

			'version' => 2.0,
			'connect_timeout' => 2.0,
			'timeout' => 4.0,
			'headers' => [
				'User-Agent' => sprintf('RovotaClient/%s (+%s)', Application::$version->basic(), Application::$server->get('server_name')),
			],
		]);
	}

	protected function getGuzzle(): Guzzle
	{
		return $this->guzzle ?? new Guzzle($this->config->toArray());
	}

}