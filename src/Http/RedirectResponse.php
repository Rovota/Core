<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http;

use Rovota\Core\Http\Enums\StatusCode;
use Rovota\Core\Routing\Enums\Scheme;
use Rovota\Core\Routing\UrlBuilder;

class RedirectResponse extends Response
{

	protected UrlBuilder $builder;
	protected string|null $path;

	// -----------------

	public function __construct(array $headers, string|null $path, array $query, StatusCode $code)
	{
		ob_clean();
		parent::__construct($headers, null, $code);

		$this->builder = new UrlBuilder();
		$this->builder->query($query);
		if ($path !== null) {
			$this->builder->path($path);
		}
	}

	public function __toString(): string
	{
		$this->header('Location', $this->builder);
		return parent::__toString();
	}

	// -----------------

	public function scheme(Scheme|string $scheme): RedirectResponse
	{
		$this->builder->scheme($scheme);
		return $this;
	}

	public function subdomain(string|null $name): RedirectResponse
	{
		$this->builder->subdomain($name);
		return $this;
	}

	public function domain(string $domain): RedirectResponse
	{
		$this->builder->domain($domain);
		return $this;
	}

	public function port(int|null $port): RedirectResponse
	{
		$this->builder->port($port);
		return $this;
	}

	public function query(string|array $key, mixed $value = null): RedirectResponse
	{
		$this->builder->query($key, $value);
		return $this;
	}

	public function path(string $path): RedirectResponse
	{
		$this->builder->path($path);
		return $this;
	}

	// -----------------

	public function route(string $name, array $params = [], array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		$this->setHttpCode($code);
		$this->builder->route($name, $params, $query);
		return $this;
	}

	public function previous(string $default = '/', array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		$this->setHttpCode($code);
		$this->builder->previous($default, $query);
		return $this;
	}

	public function next(string $default = '/', array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		$this->setHttpCode($code);
		$this->builder->next($default, $query);
		return $this;
	}

	public function intended(string $default = '/', array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		$this->setHttpCode($code);
		$this->builder->intended($default, $query);
		return $this;
	}

	public function away(string $location, array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		$this->setHttpCode($code);
		$this->builder->foreign($location, $query);
		return $this;
	}

	public function local(string $location, array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		$this->setHttpCode($code);
		$this->builder->local($location, $query);
		return $this;
	}

	public function current(array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		$this->setHttpCode($code);
		$this->builder->local(RequestManager::getRequest()->path(), $query);
		return $this;
	}

}