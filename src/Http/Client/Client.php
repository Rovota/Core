<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http\Client;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RedirectMiddleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Rovota\Core\Http\Client\Traits\ConfigModifiers;
use Rovota\Core\Kernel\Application;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Traits\Conditionable;

class Client
{
	use ConfigModifiers, Conditionable;

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
	/**
	 * @throws GuzzleException
	 */
	public function send(RequestInterface $method, array $config = []): ResponseInterface
	{
		return $this->getGuzzle()->send($method, $config);
	}

	// -----------------

	public function request(string $method, string $location, array $config = []): Request
	{
		return $this->buildRequest($method, $location, $config);
	}

	public function get(string $location, array $config = []): Request
	{
		return $this->buildRequest('GET', $location, $config);
	}

	public function delete(string $location, array $config = []): Request
	{
		return $this->buildRequest('DELETE', $location, $config);
	}

	public function head(string $location, array $config = []): Request
	{
		return $this->buildRequest('HEAD', $location, $config);
	}

	public function options(string $location, array $config = []): Request
	{
		return $this->buildRequest('OPTIONS', $location, $config);
	}

	public function patch(string $location, array $config = []): Request
	{
		return $this->buildRequest('PATCH', $location, $config);
	}

	public function post(string $location, array $config = []): Request
	{
		return $this->buildRequest('POST', $location, $config);
	}

	public function put(string $location, array $config = []): Request
	{
		return $this->buildRequest('PUT', $location, $config);
	}

	// -----------------

	protected function buildRequest(string $method, string $location, array $config = []): Request
	{
		return new Request($this->getGuzzle(), $method, $location, $config);
	}

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