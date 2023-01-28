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
use Rovota\Core\Support\Str;

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
		$raw = $this->response->getStatusCode();
		return strlen($raw) > 0 ? $raw : null;
	}

	// -----------------

	public function version(): int|float
	{
		$raw = (float) $this->response->getProtocolVersion();
		return floor($raw) == $raw ? (int)$raw : $raw;
	}

	// -----------------

	public function headers(): array
	{
		$raw = $this->response->getHeaders();
		$headers = [];

		foreach ($raw as $key => $value) {
			if (count($value) === 1) {
				$value = trim($value[0]);
			}
			$headers[$key] = $value;
		}

		return $headers;
	}

	public function hasHeader(string $name): bool
	{
		return isset($this->headers()[Str::lower($name)]);
	}

	public function header(string $name, array|string|null $default = null): array|string|null
	{
		return $this->headers()[Str::lower($name)] ?? $default;
	}

}