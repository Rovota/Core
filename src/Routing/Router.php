<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 *
 * The parameter extraction logic has been derived from bramus/router:
 * @author      Bram(us) Van Damme <bramus@bram.us>
 * @copyright   Copyright (c), 2013 Bram(us) Van Damme
 * @license     MIT public license
 */

namespace Rovota\Core\Routing;

use Closure;
use Rovota\Core\Http\Enums\StatusCode;
use Rovota\Core\Http\Response;
use Rovota\Core\Http\Throttling\LimitManager;
use Rovota\Core\Kernel\MiddlewareManager;
use Rovota\Core\Kernel\Resolver;
use Rovota\Core\Support\Collection;
use Rovota\Core\Support\Text;

/**
 * @internal
 */
final class Router
{
	/**
	 * @var Collection<int, Route>
	 */
	protected Collection $routes;
	protected Route|null $current = null;

	protected array $attributes = [];

	protected Route|null $fallback = null;

	// -----------------

	public function __construct()
	{
		$this->routes = new Collection();

		$this->setFallback(StatusCode::NotFound);
	}

	// -----------------

	public function group(Closure $routes, array $attributes): void
	{
		$original = $this->attributes;

		foreach ($attributes as $key => $value) {
			$this->attributes[$key] = match($key) {
				'middleware' => $this->getMiddlewareAttribute($value),
				'name' => $this->getNameAttribute($value),
				'prefix' => $this->getPrefixAttribute($value),
				'wheres' => $this->getWheresAttribute($value),
				'without_middleware' => $this->getWithoutMiddlewareAttribute($value),
				default => $value,
			};
		}

		call_user_func($routes);
		$this->attributes = $original;
	}

	public function addRoute(array|string $methods, string $path, mixed $target = null): Route
	{
		$route = new Route($methods, $path, $target, $this->attributes);
		$this->routes->add($route);

		return $route;
	}

	public function setFallback(mixed $target = null): Route
	{
		if ($target !== null) {
			$this->fallback = new Route(['GET'], 'not-found', $target);
		}
		return $this->fallback;
	}

	// -----------------

	public function getCurrentRoute(): Route|null
	{
		return $this->current;
	}

	public function findRouteByName(string $name): Route|null
	{
		$key = $this->routes->search(function (Route $route) use ($name) {
			return $route->getName() === $name;
		});

		return $key !== false ? $this->routes->get($key) : null;
	}

	public function findRoutesWithGroupName(string $name): Collection
	{
		return $this->routes->filter(function (Route $route) use ($name) {
			if ($route->getName() === null) {
				return false;
			}
			return str_starts_with($route->getName(), $name) && string($route->getName())->remove($name)->startsWith('.');
		});
	}

	// -----------------

	public function run(): void
	{
		$response = $this->attemptRoutes(true);

		if ($response === null) {
			$this->current = $this->fallback;
			$response = $this->executeRoute($this->fallback, []);
		}

		echo $response;

		if (request()->realMethod() === 'HEAD') {
			ob_end_clean();
		}
	}

	// -----------------

	protected function attemptRoutes(bool $regular_route = false): Response|null
	{
		$method = request()->realMethod() === 'HEAD' ? 'GET' : request()->method();
		$path = request()->path();

		foreach ($this->routes as $route) {
			if ($route->listensTo($method)) {
				if (preg_match_all('#^' . $route->getPattern() . '$#', $path, $matches, PREG_OFFSET_CAPTURE) === 1) {
					if ($regular_route) $this->current = $route;
					$parameters = $this->getExtractedParameters($matches);
					return $this->executeRoute($route, $parameters);
				}
			}
		}

		return null;
	}

	protected function executeRoute(Route $route, array $parameters): Response|null
	{
		$route->setParameters($parameters);

		if ($route->hasLimiter()) {
			LimitManager::hitAndTryLimiter($route->getLimiter());
		}

		MiddlewareManager::execute($route->getMiddleware(), $route->getWithoutMiddleware());

		if ($route->getTarget() instanceof Closure || is_array($route->getTarget())) {
			$response = Resolver::invoke($route->getTarget(), $parameters);
		} else {
			$response = $route->getTarget();
		}
		return ($response instanceof Response) ? $response : response($response);
	}

	// -----------------

	protected function getExtractedParameters(array $matches): array
	{
		// Rework matches to only contain the matches, not the original string
		$matches = array_slice($matches, 1);

		// Extract the matched URL parameters (and only the parameters)
		return array_map(function ($match, $index) use ($matches) {

			// We have a following parameter: take the substring from the current param position until the next one's position (thank you PREG_OFFSET_CAPTURE)
			if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
				if ($matches[$index + 1][0][1] > -1) {
					return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
				}
			} // We have no following parameters: return the lot

			return isset($match[0][0]) && $match[0][1] != -1 ? trim($match[0][0], '/') : null;
		}, $matches, array_keys($matches));
	}

	// -----------------

	protected function getMiddlewareAttribute(array|string $names): array
	{
		$names = is_array($names) ? $names : [$names];
		if (empty($this->attributes['middleware']) === false) {
			return array_merge($this->attributes['middleware'], $names);
		}
		return $names;
	}

	protected function getNameAttribute(string $name): string
	{
		if (empty($this->attributes['name']) === false) {
			return $this->attributes['name'].Text::finish($name, '.');
		}
		return Text::finish($name, '.');
	}

	protected function getPrefixAttribute(string $prefix): string
	{
		$prefix = Text::start(Text::trim($prefix, '/'), '/');
		if (empty($this->attributes['prefix']) === false) {
			return $this->attributes['prefix'].$prefix;
		}
		return $prefix;
	}

	protected function getWheresAttribute(array $wheres): array
	{
		if (empty($this->attributes['wheres']) === false) {
			return array_merge($this->attributes['wheres'], $wheres);
		}
		return $wheres;
	}

	protected function getWithoutMiddlewareAttribute(array|string $names): array
	{
		$names = is_array($names) ? $names : [$names];
		if (empty($this->attributes['without_middleware']) === false) {
			return array_merge($this->attributes['without_middleware'], $names);
		}
		return $names;
	}

}