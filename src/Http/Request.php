<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http;

use Rovota\Core\Facades\Route;
use Rovota\Core\Http\Traits\RequestInput;
use Rovota\Core\Routing\Route as RouteObject;
use Rovota\Core\Structures\ErrorBucket;
use Rovota\Core\Support\Traits\Errors;
use Rovota\Core\Validation\Traits\RequestValidation;

final class Request
{
	use RequestInput, RequestValidation, Errors;

	// -----------------

	public function __construct(string|null $body, array $post, array $query, array $headers)
	{
		$this->errors = new ErrorBucket();
	}

	// ---------------

	public function route(): RouteObject|null
	{
		return Route::current();
	}

	public function routeIsNamed(string $name): bool
	{
		if (str_ends_with($name, '*')) {
			return str_starts_with(Route::currentName() ?? '', str_replace('*', '', $name));
		}
		return Route::currentName() === $name;
	}

}