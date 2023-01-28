<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http\Client\Traits;

use Rovota\Core\Http\Enums\StatusCode;

trait ResponseChecks
{

	public function failed(): bool
	{
		return $this->serverError() || $this->clientError();
	}

	// -----------------
	// 2xx

	public function successful(): bool
	{
		return $this->status()->value >= 200 && $this->status()->value < 300;
	}

	public function ok(): bool
	{
		return $this->status() === StatusCode::Ok;
	}

	// -----------------
	// 3xx

	public function redirect(): bool
	{
		return $this->status()->value >= 300 && $this->status()->value < 400;
	}

	// -----------------
	// 4xx

	public function clientError(): bool
	{
		return $this->status()->value >= 400 && $this->status()->value < 500;
	}

	public function unauthorized(): bool
	{
		return $this->status() === StatusCode::Unauthorized;
	}

	public function paymentRequired(): bool
	{
		return $this->status() === StatusCode::PaymentRequired;
	}

	public function forbidden(): bool
	{
		return $this->status() === StatusCode::Forbidden;
	}

	public function notFound(): bool
	{
		return $this->status() === StatusCode::NotFound;
	}

	public function tooManyRequests(): bool
	{
		return $this->status() === StatusCode::TooManyRequests;
	}

	// -----------------
	// 5xx

	public function serverError(): bool
	{
		return $this->status()->value >= 500;
	}

	public function serviceUnavailable(): bool
	{
		return $this->status() === StatusCode::ServiceUnavailable;
	}

	public function notImplemented(): bool
	{
		return $this->status() === StatusCode::NotImplemented;
	}

}