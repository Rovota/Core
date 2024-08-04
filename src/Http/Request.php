<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http;

use Rovota\Core\Facades\Cookie;
use Rovota\Core\Facades\Route;
use Rovota\Core\Http\Traits\RequestInput;
use Rovota\Core\Routing\Route as RouteObject;
use Rovota\Core\Structures\ErrorBucket;
use Rovota\Core\Support\Traits\Errors;
use Rovota\Core\Validation\Enums\FilterAction;
use Rovota\Core\Validation\FilterManager;
use Rovota\Core\Validation\Traits\RequestValidation;

final class Request
{
	use RequestInput, RequestValidation, Errors;

	protected array $client_names;
	protected array $client_robots;

	// -----------------

	public function __construct(string|null $body, array $post, array $query, array $headers)
	{
		$this->client_names = include base_path('vendor/rovota/core/src/Http/client_names.php');
		$this->client_robots = include base_path('vendor/rovota/core/src/Http/client_robots.php');

		$this->errors = new ErrorBucket();
	}

	// -----------------

	/**
	 * Uses the experimental 'Sec-CH-UA' HTTP header.
	 * For non-supported clients, it will guess the client name.
	 */
	public function client(string|null $default = null): string|null
	{
		$client = null;
		if ($this->hasHeader('Sec-CH-UA')) {
			$names = array_reduce(explode(',', trim($this->header('Sec-CH-UA'))),
				function ($carry, $element) {
					$brand = Str::remove(Str::beforeLast($element, ';'), '"');
					$version = str_contains($element, ';v=') ? Str::afterLast($element, ';v=') : '';
					if (Str::containsNone($brand, ['Brand', 'Chromium'])) {
						$carry[trim($brand)] = (int) Str::remove($version, '"');
					}
					return $carry;
				},[]
			);
			$client = array_key_first($names);
		}

		if ($client !== null) {
			return $client;
		}

		foreach ($this->client_names as $client => $name) {
			if (str_contains($this->header('User-Agent', ''), $client)) {
				return $name;
			}
		}
		return $default;
	}

	public function ipAllowed(string|null $ip = null): bool
	{
		$filter = FilterManager::get('access_control');
		if ($filter !== null && $filter->action === FilterAction::Block) {
			return Arr::contains($filter->values, $ip ?? $this->ip()) === false;
		}
		if ($filter !== null && $filter->action === FilterAction::Allow) {
			return Arr::contains($filter->values, $ip ?? $this->ip());
		}
		return true;
	}

	public function isBot(): bool
	{
		return text($this->header('User-Agent', ''))->lower()->containsAny($this->client_robots);
	}

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

	// -----------------




	public function cookie(string $name, string|null $default = null): string|null
	{
		$cookie = Cookie::findReceived($name);
		return $cookie !== null ? $cookie->value : $default;
	}

	public function hasCookie(string $name): bool
	{
		return Cookie::isReceived($name);
	}

}