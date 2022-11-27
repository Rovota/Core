<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http;

use Rovota\Core\Http\Enums\StatusCode;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Str;
use Throwable;

class ApiErrorResponse extends Response
{

	public function __construct(array $headers, Throwable|ApiError|array $error, StatusCode $code)
	{
		$errors = [];

		if (is_array($error)) {
			foreach ($error as $item) {
				$errors[] = $this->getErrorAsArray($item);
			}
		} else {
			$errors[] = $this->getErrorAsArray($error);
		}

		parent::__construct($headers, [
			'timestamp' => new Moment(),
			'errors' => $errors
		], $code);
	}

	// -----------------

	protected function getErrorAsArray(Throwable|ApiError $error): array
	{
		return [
			'type' => Str::afterLast($error::class, '\\'),
			'code' => $error->getCode(),
			'message' => match (true) {
				$error instanceof ApiError => __($error->getMessage(), $error->getParameters()),
				default => __($error->getMessage()),
			},
		];
	}

}