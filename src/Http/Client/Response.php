<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http\Client;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Rovota\Core\Http\Client\Traits\ResponseChecks;
use Rovota\Core\Http\Enums\StatusCode;

final class Response
{
	use ResponseChecks;

	// -----------------

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

	// -----------------

	public function status(): StatusCode
	{
		return StatusCode::tryFrom($this->response->getStatusCode());
	}

	public function reason(): string|null
	{
		$reason = $this->response->getStatusCode();
		return strlen($reason) > 0 ? $reason : null;
	}

}