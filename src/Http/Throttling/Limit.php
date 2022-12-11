<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 *
 * Inspired by Laravel rate limiting.
 */

namespace Rovota\Core\Http\Throttling;

use Rovota\Core\Cache\CacheManager;
use Rovota\Core\Http\RequestManager;
use Rovota\Core\Http\ResponseManager;
use Rovota\Core\Http\Throttling\Enums\IdentifierType;
use Rovota\Core\Http\Throttling\Enums\PeriodType;
use Rovota\Core\Support\Traits\Conditionable;

final class Limit
{
	use Conditionable;

	protected string $cache;

	protected string $name;
	protected string $identifier;
	protected IdentifierType $identifier_type;

	protected int $limit;
	protected int $period;
	protected PeriodType $period_type;

	protected mixed $response = null;
	protected bool $with_headers = false;

	// -----------------

	public function __construct(string $identifier, int $limit, int $period, PeriodType $period_type)
	{
		$this->cache = CacheManager::getDefault();

		$this->name = '';
		$this->identifier = $identifier;
		$this->identifier_type = IdentifierType::Global;

		$this->limit = $limit;
		$this->period = $period;
		$this->period_type = $period_type;

		$this->response = fn () => 429;
	}

	// -----------------

	public static function none(): Limit
	{
		return new Limit('', PHP_INT_MAX, 1, PeriodType::Second);
	}

	public static function perSecond(int $limit): Limit
	{
		return new Limit('', $limit, 1, PeriodType::Second);
	}

	public static function perMinute(int $limit): Limit
	{
		return new Limit('', $limit, 1, PeriodType::Minute);
	}

	public static function perHour(int $limit): Limit
	{
		return new Limit('', $limit, 1, PeriodType::Hour);
	}

	public static function perDay(int $limit): Limit
	{
		return new Limit('', $limit, 1, PeriodType::Day);
	}

	public static function perWeek(int $limit): Limit
	{
		return new Limit('', $limit, 1, PeriodType::Week);
	}

	// -----------------

	public function by(string $identifier): Limit
	{
		$this->identifier_type = IdentifierType::Custom;
		$this->identifier = $identifier;
		return $this;
	}

	public function byIP(): Limit
	{
		$this->identifier_type = IdentifierType::IP;
		$this->identifier = RequestManager::getRequest()->ip();
		return $this;
	}

	public function byToken(): Limit
	{
		$this->identifier_type = IdentifierType::Token;
		$this->identifier = RequestManager::getRequest()->authToken() ?? '';
		return $this;
	}

	// -----------------

	public function response(callable $callback): Limit
	{
		$this->response = $callback;
		return $this;
	}

	public function withHeaders(): Limit
	{
		$this->with_headers = true;
		return $this;
	}

	public function cache(string $name): Limit
	{
		if (CacheManager::isDefined($name)) {
			$this->cache = $name;
		}
		return $this;
	}

	// -----------------

	public function name(): string
	{
		return $this->name;
	}

	public function identifier(): string
	{
		return $this->identifier;
	}

	public function identifierType(): IdentifierType
	{
		return $this->identifier_type;
	}

	public function limit(): int
	{
		return $this->limit;
	}

	public function period(): int
	{
		return $this->period;
	}

	public function periodType(): PeriodType
	{
		return $this->period_type;
	}

	// -----------------

	public function hit(): void
	{
		$key = $this->getKeyName();
		$cache = CacheManager::get($this->cache);
		if ($cache->has($key)) {
			$cache->increment($key);
		} else {
			$cache->set($key, 1, $this->getSecondsFromPeriod($this->period));
		}
	}

	public function attempts(): int
	{
		return CacheManager::get($this->cache)->get($this->getKeyName(), 0);
	}

	public function tooManyAttempts(): bool
	{
		return $this->attempts() > $this->limit;
	}

	public function reset(): void
	{
		CacheManager::get($this->cache)->remove($this->getKeyName());
	}

	public function remaining(): int
	{
		return max($this->limit - $this->attempts(), 0);
	}

	// -----------------
	// Internal usage

	/**
	 * @internal
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * @internal
	 */
	public function setHeadersWhenEnabled(): void
	{
		ResponseManager::addHeaders([
			'X-RateLimit-Limit' => $this->limit,
			'X-RateLimit-Remaining' => $this->remaining(),
		]);
	}

	/**
	 * @internal
	 */
	public function getResponse(): mixed
	{
		return $this->response;
	}

	// -----------------

	protected function getKeyName(): string
	{
		return implode(':', ['limiter', $this->name, $this->identifier]);
	}

	protected function getSecondsFromPeriod(int $period): int
	{
		return match($this->period_type) {
			PeriodType::Second => $period,
			PeriodType::Minute => $period * 60,
			PeriodType::Hour => $period * 3600,
			PeriodType::Day => $period * 86400,
			PeriodType::Week => $period * 7 * 86400,
		};
	}

}