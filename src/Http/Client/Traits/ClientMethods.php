<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http\Client\Traits;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Rovota\Core\Http\Client\Request;

trait ClientMethods
{
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

}