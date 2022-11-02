<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Routing;

use Rovota\Core\Support\Text;
use Rovota\Core\Support\Traits\Conditionable;

final class UrlBuilder
{
	use Conditionable;

	protected string $scheme;
	protected string $domain;
	protected int $port;
	protected array $query;

	// -----------------

	public function __construct()
	{
		$this->domain(request()->targetHost());
		$this->scheme = 'https';
		$this->query = [];
		$this->port = request()->port();
	}

	// -----------------

	public function scheme(string $scheme): UrlBuilder
	{
		$this->scheme = $scheme;
		return $this;
	}

	public function domain(string $domain): UrlBuilder
	{
		$this->domain = string($domain)->after('://')->before('/');
		return $this;
	}

	public function subdomain(string $subdomain): UrlBuilder
	{
		if (strlen($subdomain) === 0 || $subdomain === 'www' || $subdomain === '.') {
			return $this;
		}
		$this->domain = string($this->domain)->prepend(trim($subdomain, '.').'.');
		return $this;
	}

	public function port(int $port): UrlBuilder
	{
		if ($port !== 80 && $port !== 443) {
			$this->port = $port;
		}
		return $this;
	}

	public function query(array $items): UrlBuilder
	{
		$this->query = $items;
		return $this;
	}

	public function queryItem(string $key, mixed $value): UrlBuilder
	{
		$this->query[$key] = (string)$value;
		return $this;
	}

	// -----------------

	public function external(string $location, array $query = []): string
	{
		$this->query = array_merge($this->query, $query);
		$location = str_contains($location, '://') ? $location : 'https://'.$location;
		return $this->buildUrl($location, false);
	}

	public function path(string $path, array $query = []): string
	{
		$this->query = array_merge($this->query, $query);
		return $this->buildUrl($path);
	}

	public function route(string $name, array $params = [], array $query = []): string
	{
		$this->query = array_merge($this->query, $query);
		$route = RouteManager::findRouteByName($name);

		if ($route === null) {
			$this->query = [];
			return $this->buildUrl('/');
		}

		$path = UrlBuilder::getPathUsingParams($route->getPath(), $params);
		return $this->buildUrl($path);
	}

	// -----------------

	public static function getPathUsingParams(string $path, array $params): string
	{
		if (empty($params) === false) {
			if (array_is_list($params)) {
				$path = preg_replace('/{(.*?)}/', '{parameter}', $path);
				$path = Text::replaceSequential($path, '{parameter}', $params);
			} else {
				foreach ($params as $key => $value) {
					$path = str_replace(sprintf('{%s}', $key), $value, $path);
				}
			}
		}
		return $path;
	}

	/**
	 * Returns a formatted query string using the items in the array.
	 */
	public static function arrayToQuery(array $fields = [], bool $encode = true): string
	{
		$items = '';
		foreach ($fields as $key => $value) {
			$value = (string)$value;
			if (Text::length($value) > 0) {
				$value = $encode ? rawurlencode($value) : $value;
				$items .= sprintf('%s%s=%s', (Text::length($items) > 0) ? '&' : '', $key, $value);
			}
		}
		return (Text::length($items) > 0) ? '?'.$items : '';
	}

	// -----------------

	protected function buildUrl(string $path, bool $full = true): string
	{
		$query = UrlBuilder::arrayToQuery($this->query);
		$port = ($this->port !== 80 && $this->port !== 443) ? ':'.$this->port : '';
		$address = sprintf('%s://%s%s', $this->scheme, $this->domain, $port);
		$path = trim($path, '/');

		return trim($full ? sprintf('%s/%s',  $address, $path) : $path, '/').$query;
	}

}