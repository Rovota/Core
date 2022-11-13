<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http\Client;

use GuzzleHttp\Client as Guzzle;
use Rovota\Core\Http\Client\Traits\ConfigModifiers;
use Rovota\Core\Support\Bucket;
use Rovota\Core\Support\Traits\Conditionable;

final class Request
{
	use ConfigModifiers, Conditionable;

	// -----------------

	protected Guzzle $guzzle;

	protected string $method;

	protected string $location;

	protected Bucket $config;

	// -----------------

	public function __construct(Guzzle $guzzle, string $method, string $location, array $config)
	{
		$this->guzzle = $guzzle;
		$this->method = $method;
		$this->location = $location;
		$this->config = new Bucket($config);
	}

	// -----------------

	/**
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function execute(): Response
	{
		$response = $this->guzzle->request($this->method, $this->location, $this->config->all());
		return new Response($response);
	}

}