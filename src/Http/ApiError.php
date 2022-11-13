<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http;

class ApiError
{

	protected int|null $code = null;

	protected string|null $message = null;

	protected array $parameters = [];

	// -----------------

	public function __construct(string|null $message = null, int|null $code = null, array $parameters = [])
	{
		if ($message !== null) {
			$this->message = $message;
		}
		if ($code !== null) {
			$this->code = $code;
		}

		foreach ($parameters as $key => $value) {
			$this->parameters[$key] = $value;
		}
	}

	// -----------------

	public function getCode(): int
	{
		return $this->code ?? 0;
	}

	public function getMessage(): string
	{
		return $this->message ?? 'There is no information available about this error.';
	}

	public function getParameters(): array
	{
		return $this->parameters;
	}

}