<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http\Client;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use Rovota\Core\Http\Client\Traits\ConfigModifiers;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Traits\Conditionable;

final class Request
{
	use ConfigModifiers, Conditionable;

	// -----------------

	protected Guzzle $guzzle;

	protected string $method;

	protected string $location;

	protected Bucket $config;

	protected array $query = [];

	// -----------------

	public function __construct(Guzzle $guzzle, string $method, string $location, array $config, array $query)
	{
		$this->guzzle = $guzzle;
		$this->method = $method;
		$this->location = $location;
		$this->config = new Bucket($config);
		$this->query = $query;
	}

	// -----------------

	/**
	 * @throws GuzzleException
	 */
	public function execute(): Response
	{
		$response = $this->guzzle->request($this->method, $this->location, array_merge($this->config->toArray(), ['query' => $this->query]));
		return new Response($response);
	}

}