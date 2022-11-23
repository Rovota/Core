<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http;

use Rovota\Core\Http\Enums\StatusCode;
use Rovota\Core\Routing\UrlBuilder;
use Rovota\Core\Session\SessionManager;

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
		$this->path = $path;
	}

	public function __toString(): string
	{
		if ($this->path !== null) {
			$this->header('Location', $this->builder->path($this->path));
		}
		return parent::__toString();
	}

	// -----------------

	public function domain(string $domain): RedirectResponse
	{
		$this->builder->domain($domain);
		return $this;
	}

	public function subdomain(string $domain): RedirectResponse
	{
		$this->builder->subdomain($domain);
		return $this;
	}

	// -----------------

	public function path(string $path, array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		$this->setHttpCode($code);
		$this->header('Location', $this->builder->path($path, $query));
		return $this;
	}

	public function away(string $location, array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		$this->setHttpCode($code);
		$this->header('Location', $this->builder->external($location, $query));
		return $this;
	}

	public function previous(array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		$this->setHttpCode($code);
		$location = SessionManager::get()->pull('location.previous') ?? request()->referrer();
		$this->header('Location', $this->builder->external($location, $query));
		return $this;
	}

	public function intended(string $default = '/', array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		$this->setHttpCode($code);
		$location = SessionManager::get()->pull('location.intended') ?? $default;
		$this->header('Location', $this->builder->external($location, $query));
		return $this;
	}

	public function continue(array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		$this->setHttpCode($code);
		$location = SessionManager::get()->pull('location.continue');
		$this->header('Location', $this->builder->external($location, $query));
		return $this;
	}

	public function route(string $name, array $params = [], array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		$this->setHttpCode($code);
		$this->header('Location', $this->builder->route($name, $params, $query));
		return $this;
	}

}