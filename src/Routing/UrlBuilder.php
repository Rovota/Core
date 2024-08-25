<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Routing;

use Rovota\Core\Http\RequestManager;

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

}