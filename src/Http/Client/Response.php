<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Http\Client;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Rovota\Core\Support\Collection;

final class Response
{

	protected ResponseInterface $response;

	// -----------------

	public function __construct(ResponseInterface $response)
	{
		$this->response = $response;
	}

	// -----------------

	public function psr(): ResponseInterface
	{
		return $this->response;
	}

	// -----------------

	public function body(): StreamInterface
	{
		return $this->response->getBody();
	}

	public function string(): string
	{
		return $this->body()->getContents();
	}

	public function json(): string|null
	{
		$contents = $this->string();
		if (json_decode($contents) !== null) {
			return $contents;
		}
		return null;
	}

	public function jsonAsArray(): array
	{
		$json = json_decode($this->json() ?? '', true);
		if ($json !== null) {
			return is_array($json) ? $json : [$json];
		}
		return [];
	}

	public function jsonAsCollection(): Collection
	{
		return new Collection($this->jsonAsArray());
	}

}