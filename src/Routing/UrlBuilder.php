<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Routing;

use Rovota\Core\Http\RequestManager;
use Rovota\Core\Session\SessionManager;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\UrlTools;

final class UrlBuilder
{

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

}