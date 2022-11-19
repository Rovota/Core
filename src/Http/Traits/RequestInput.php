<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http\Traits;

use BackedEnum;
use DateTime;
use DateTimeZone;
use Rovota\Core\Facades\Session;
use Rovota\Core\Http\RequestData;
use Rovota\Core\Http\UploadedFile;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Structures\Map;
use Rovota\Core\Support\Moment;

trait RequestInput
{

	public readonly string|null $body;
	public readonly RequestData $post;
	public readonly RequestData $query;

	protected RequestData|null $input_data = null;

	// -----------------

	public function all(): array
	{
		return $this->getInputData()->all();
	}

	public function except(string|array $keys): RequestData
	{
		return $this->getInputData()->except($keys);
	}

	public function filled(string|array $keys): bool
	{
		return $this->getInputData()->filled($keys);
	}

	public function get(string $key, mixed $default = null): mixed
	{
		return $this->getInputData()->get($key, $default);
	}

	public function has(string|array $key): bool
	{
		return $this->getInputData()->has($key);
	}

	public function missing(string|array $key): bool
	{
		return $this->getInputData()->missing($key);
	}

	public function only(string|array $keys): RequestData
	{
		return $this->getInputData()->only($keys);
	}

	public function whenFilled(string $key, callable $callback): mixed
	{
		if ($this->getInputData()->filled($key)) {
			return $callback();
		}
		return null;
	}

	public function whenHas(string $key, callable $callback): mixed
	{
		if ($this->getInputData()->has($key)) {
			return $callback();
		}
		return null;
	}

	public function whenMissing(string $key, callable $callback): mixed
	{
		if ($this->getInputData()->has($key) === false) {
			return $callback();
		}
		return null;
	}

	// -----------------

	public function array(string $key, array $default = []): array
	{
		return $this->getInputData()->array($key, $default);
	}

	public function bool(string $key, bool $default = false): bool
	{
		return $this->getInputData()->bool($key, $default);
	}

	public function date(string $key, DateTimeZone|null $timezone = null): DateTime|null
	{
		return $this->getInputData()->date($key, $timezone);
	}

	public function enum(string $key, string $class, BackedEnum|null $default = null): BackedEnum|null
	{
		return $this->getInputData()->enum($key, $class, $default);
	}

	public function float(string $key, float $default = 0.00): float
	{
		return $this->getInputData()->float($key, $default);
	}

	public function int(string $key, int $default = 0): int
	{
		return $this->getInputData()->int($key, $default);
	}

	public function moment(string $key, DateTimeZone|string|null $timezone = null): Moment|null
	{
		return $this->getInputData()->moment($key, $timezone);
	}

	public function string(string $key, string $default = ''): string
	{
		return $this->getInputData()->string($key, $default);
	}

	// -----------------

	public function keep(): void
	{
		Session::flashMany($this->getInputData()->all());
	}

	public function keepOnly(string|array $keys): void
	{
		Session::flashMany($this->getInputData()->only($keys)->all());
	}

	public function keepExcept(string|array $keys): void
	{
		Session::flashMany($this->getInputData()->except($keys)->all());
	}

	public function old(string $key, mixed $default = null): mixed
	{
		return Session::pull($key, $default);
	}

	// -----------------

	public function hasFile(string $key): bool
	{
		return $this->post->has($key) && $this->post->get($key) instanceof UploadedFile;
	}

	public function file(string $key): UploadedFile|null
	{
		$file = $this->post->get($key);
		return $file instanceof UploadedFile ? $file : null;
	}

	public function files(string $key): array
	{
		$files = $this->post->get($key, []);
		$result = [];

		foreach (is_array($files) ? $files : [] as $key => $file) {
			if ($file instanceof UploadedFile) {
				$result[$key] = $file;
			}
		}

		return $result;
	}

	// -----------------

	public function body(): string|null
	{
		return $this->body;
	}

	public function json(): string|null
	{
		if (json_decode($this->body ?? '') !== null) {
			return $this->body;
		}
		return null;
	}

	public function jsonAsArray(): array
	{
		$json = json_decode($this->body ?? '', true);
		if ($json !== null) {
			return is_array($json) ? $json : [$json];
		}
		return [];
	}

	public function jsonAsBucket(): Bucket
	{
		return new Bucket($this->jsonAsArray());
	}

	public function jsonAsMap(): Map
	{
		return new Map($this->jsonAsArray());
	}

	// -----------------

	protected function getInputData(): RequestData
	{
		if ($this->input_data === null) {
			$this->input_data = new RequestData(array_merge($this->query->all(), $this->post->all()));
		}
		return $this->input_data;
	}

}