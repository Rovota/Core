<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Routing;

use Rovota\Core\Auth\AuthManager;
use Rovota\Core\Routing\Traits\RouteModifiers;
use Rovota\Core\Support\Text;
use Rovota\Core\Support\Traits\Conditionable;

final class Route
{
	use Conditionable, RouteModifiers;

	// Required
	protected array $methods;
	protected string $path;
	protected array $parameters;

	// Optional
	protected mixed $target = null;
	protected string|null $auth = null;
	protected string|null $controller = null;
	protected array $middleware = [];
	protected string|null $name = null;
	protected string|null $prefix = null;
	protected string|null $limiter = null;
	protected array $wheres = [];
	protected array $without_middleware = [];

	// Cache
	protected string|null $compiled = null;

	// -----------------

	public function __construct(array|string $methods, string $path, mixed $target = null, array $attributes = [])
	{
		$this->setMethods($methods);
		$this->setAttributes($attributes);

		if ($target !== null) {
			$this->target = is_string($target) ? [$this->controller ?? Controller::class, $target] : $target;
		}

		$path = trim($path, '/');
		$prefix = trim($this->prefix ?? '', '/');
		$this->path = Text::start(empty($path) ? $prefix : implode('/', [$prefix, $path]), '/');
	}

	// -----------------

	public function getAuthProvider(): string
	{
		return $this->auth ?? AuthManager::getDefault();
	}

	public function getMiddleware(): array
	{
		return $this->middleware;
	}

	public function getName(): string|null
	{
		return $this->name;
	}

	public function getPrefix(): string|null
	{
		return $this->prefix;
	}

	public function getTarget(): mixed
	{
		return $this->target;
	}

	public function getLimiter(): string|null
	{
		return $this->limiter;
	}

	public function getWhere(string $name): string|null
	{
		return $this->wheres[$name] ?? null;
	}

	public function getWheres(): array
	{
		return $this->wheres;
	}

	public function getWithoutMiddleware(): array
	{
		return $this->without_middleware;
	}

	// -----------------

	public function getMethods(): array
	{
		return $this->methods;
	}

	public function getPath(): string
	{
		return $this->path;
	}

	public function getParameters(): array
	{
		return $this->parameters;
	}

	// -----------------

	public function getUrl(array $params = [], array $query = []): string|null
	{
		$builder = new UrlBuilder();
		return $builder->route($this->name, $params, $query);
	}

	public function getPattern(): string
	{
		return $this->compiled ?? $this->compilePattern();
	}

	// -----------------

	public function listensTo(string $method, string|null $path = null): bool
	{
		if (in_array(strtoupper($method), $this->methods) === false) {
			return false;
		}

		if (is_string($path)) {
			return preg_match_all('#^' . $this->getPattern() . '$#', $path) === 1;
		}

		return true;
	}

	public function hasLimiter(): bool
	{
		return $this->limiter !== null;
	}

	public function setParameters(array $parameters): void
	{
		$this->parameters = $parameters;
	}

	// -----------------

	protected function compilePattern(): string
	{
		$pattern = $this->path;

		foreach ($this->wheres as $parameter => $expression) {
			$pattern = str_replace(sprintf('{%s}', $parameter), sprintf('(%s)', $expression), $pattern);
		}

		return preg_replace('/\/{(.*?)}/', '/(.*?)', $pattern);
	}

	protected function setMethods(array|string $methods): void
	{
		$this->methods = is_array($methods) ? $methods : explode('|', $methods);
	}

	protected function setAttributes(array $attributes): void
	{
		foreach ($attributes as $name => $value) {
			if (property_exists($this, $name)) {
				$this->{$name} = match($name) {
					'name' => trim($value, '.'),
					default => $value,
				};
			}
		}
	}

}