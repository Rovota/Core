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

	public function isFailure(): bool
	{
		return $this->isServerError() || $this->isClientError();
	}

	// -----------------
	// 2xx

	public function isSuccess(): bool
	{
		return $this->status()->value >= 200 && $this->status()->value < 300;
	}

	public function isOk(): bool
	{
		return $this->status() === StatusCode::Ok;
	}

	// -----------------
	// 3xx

	public function isRedirect(): bool
	{
		return $this->status()->value >= 300 && $this->status()->value < 400;
	}

	// -----------------
	// 4xx

	public function isClientError(): bool
	{
		return $this->status()->value >= 400 && $this->status()->value < 500;
	}

	public function isUnauthorized(): bool
	{
		return $this->status() === StatusCode::Unauthorized;
	}

	public function isPaymentRequired(): bool
	{
		return $this->status() === StatusCode::PaymentRequired;
	}

	public function isForbidden(): bool
	{
		return $this->status() === StatusCode::Forbidden;
	}

	public function isNotFound(): bool
	{
		return $this->status() === StatusCode::NotFound;
	}

	public function isTooManyRequests(): bool
	{
		return $this->status() === StatusCode::TooManyRequests;
	}

	// -----------------
	// 5xx

	public function isServerError(): bool
	{
		return $this->status()->value >= 500;
	}

	public function isServiceUnavailable(): bool
	{
		return $this->status() === StatusCode::ServiceUnavailable;
	}

	public function isNotImplemented(): bool
	{
		return $this->status() === StatusCode::NotImplemented;
	}

}