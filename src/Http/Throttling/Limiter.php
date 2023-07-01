<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 *
 * Inspired by Laravel rate limiting.
 */

namespace Rovota\Core\Http\Throttling;

use Closure;
use Rovota\Core\Http\RequestManager;
use Rovota\Core\Http\Response;
use Rovota\Core\Kernel\Resolver;

final class Limiter
{

	protected array $limits;

	protected string $label;

	// -----------------

	public function __construct(string $name, Closure|Limit $limits)
	{
		if ($limits instanceof Limit) {
			$limits->setName($name);
			$this->limits[$limits->name()] = $limits;
			return;
		}
		$limits = $limits(RequestManager::getRequest());
		$limits = $limits instanceof Limit ? [$limits] : $limits;
		foreach ($limits as $number => $limit) {
			$limit->setName($name.'-'.$number);
			$this->limits[$limit->name()] = $limit;
		}
		$this->label = $name;
	}

	// -----------------

	public function label(string $label): void
	{
		$this->label = $label;
	}

	// -----------------

	public function limit(string $name): Limit|null
	{
		return $this->limits[$name] ?? null;
	}

	/**
	 * @returns array<string, Limit>
	 */
	public function limits(): array
	{
		return $this->limits;
	}

	// -----------------

	public function hit(): void
	{
		foreach ($this->limits as $limit) {
			$limit->hit();
		}
	}

	public function attempts(): int
	{
		foreach ($this->limits as $limit) {
			return $limit->attempts();
		}
		return 0;
	}

	public function tooManyAttempts(): bool
	{
		foreach ($this->limits as $limit) {
			if ($limit->tooManyAttempts()) {
				return true;
			}
		}
		return false;
	}

	public function reset(): void
	{
		foreach ($this->limits as $limit) {
			$limit->reset();
		}
	}

	/**
	 * @returns array<string, int>
	 */
	public function remaining(): array
	{
		$remaining = [];
		foreach ($this->limits as $name => $limit) {
			$remaining[$name] = $limit->remaining();
		}
		return $remaining;
	}

	// -----------------

	/**
	 * @internal
	 */
	public function hitAndTry(): void
	{
		foreach ($this->limits as $limit) {
			$limit->hit();
			$limit->setHeadersWhenEnabled();
			if ($limit->tooManyAttempts()) {
				$response = Resolver::invoke($limit->getResponse());
				echo ($response instanceof Response) ? $response : response($response);
				exit;
			}
		}
	}

}