<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Routing;

use Rovota\Core\Http\RequestManager;
use Rovota\Core\Routing\Enums\Scheme;
use Rovota\Core\Session\SessionManager;
use Rovota\Core\Support\Str;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\UrlTools;

final class UrlBuilder
{
	use Conditionable;

	protected UrlObject $url;

	// -----------------

	public function __construct()
	{
		$this->url = UrlObject::fromArray([
			'scheme' => Scheme::Https,
			'port' => RequestManager::getRequest()->port(),
		]);
	}

	public function __toString(): string
	{
		if ($this->url->domain === null) {
			$this->domain(RequestManager::getRequest()->targetHost());
		}

		return $this->url;
	}

	// -----------------

	public function scheme(Scheme|string $scheme): UrlBuilder
	{
		$this->url->scheme = is_string($scheme) ? (Scheme::tryFrom($scheme) ?? Scheme::Https) : $scheme;
		return $this;
	}

	public function subdomain(string|null $name): UrlBuilder
	{
		if ($this->url->domain === null) {
			$this->domain(RequestManager::getRequest()->targetHost());
		}

		if ($name === null || mb_strlen($name) === 0 || $name === 'www' || $name === '.') {
			$this->url->subdomain = null;
			return $this;
		}

		$this->url->subdomain = $name;
		return $this;
	}

	public function domain(string $name): UrlBuilder
	{
		if (Str::occurrences($name, '.') > 1) {
			$subdomain = Str::before($name, '.');
			$this->url->subdomain = $subdomain;
			$name = Str::remove($name, $subdomain.'.');
		}

		$this->url->domain = $name;
		return $this;
	}

	public function port(int|null $port): UrlBuilder
	{
		if ($port === 80 || $port === 443) {
			$this->url->port = null;
			return $this;
		}

		$this->url->port = $port;
		return $this;
	}

	public function query(string|array $key, mixed $value = null): UrlBuilder
	{
		if (is_array($key)) {
			foreach ($key as $name => $value) {
				$this->url->query[$name] = $value;
			}
		} else {
			$this->url->query[$key] = $value;
		}

		return $this;
	}

	public function path(string $path): UrlBuilder
	{
		$path = trim($path, '/');
		if (mb_strlen($path) === 0 || $path === '/') {
			$this->url->path = null;
			return $this;
		}

		$this->url->path = trim($path);
		return $this;
	}

	// -----------------

	public function route(string $name, array $params = [], array $query = []): UrlBuilder
	{
		$route = RouteManager::findRouteByName($name);
		$this->domain(RequestManager::getRequest()->targetHost());

		if ($route === null) {
			$this->path('/');
			$this->url->query = [];
			return $this;
		}

		$path = UrlTools::getPathUsingParams($route->getPath(), $params);
		$this->path($path);
		$this->query($query);
		return $this;
	}

	public function previous(string $default = '/', array $query = []): UrlBuilder
	{
		$location = SessionManager::get()->pull('location.previous', RequestManager::getRequest()->referrer() ?? $default);
		return $this->foreign($location, $query);
	}

	public function next(string $default = '/', array $query = []): UrlBuilder
	{
		$location = SessionManager::get()->pull('location.next', $default);
		return $this->foreign($location, $query);
	}

	public function intended(string $default = '/', array $query = []): UrlBuilder
	{
		$location = SessionManager::get()->pull('location.intended', $default);
		return $this->foreign($location, $query);
	}

	public function foreign(string $location, array $query = []): UrlBuilder
	{
		$data = $this->url->query;
		$this->url = UrlObject::from($location);
		$this->url->query = array_merge($data, $query);
		return $this;
	}

	public function local(string $location, array $query = []): UrlBuilder
	{
		$data = $this->url->query;
		$this->url = UrlObject::from(Str::start($location, '/'));
		$this->url->query = array_merge($data, $query);
		$this->url->domain = RequestManager::getRequest()->targetHost();
		return $this;
	}

}