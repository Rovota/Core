<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Http\Traits;

use BackedEnum;
use DateTime;
use DateTimeZone;
use Rovota\Core\Facades\Session;
use Rovota\Core\Http\UploadedFile;
use Rovota\Core\Support\Bucket;
use Rovota\Core\Support\Collection;
use Rovota\Core\Support\Moment;

trait RequestInput
{

	public readonly string|null $body;
	public readonly Bucket $post;
	public readonly Bucket $query;

	protected Bucket|null $input_data = null;

	// -----------------

	public function all(): array
	{
		return $this->getInputData()->all();
	}

	public function collect(): Collection
	{
		return $this->getInputData()->collect();
	}

	public function input(string $key, mixed $default = null): mixed
	{
		return $this->getInputData()->get($key, $default);
	}

	public function only(string|array $keys, bool $allow_null = true): array
	{
		return $this->getInputData()->only($keys, $allow_null);
	}

	public function except(string|array $keys): array
	{
		return $this->getInputData()->except($keys);
	}

	public function merge(array $keys): void
	{
		$this->getInputData()->merge($keys);
	}

	public function mergeIfMissing(array $keys): void
	{
		$this->getInputData()->mergeIfMissing($keys);
	}

	public function has(string $key): bool
	{
		return $this->getInputData()->has($key);
	}

	public function hasAll(array $keys): bool
	{
		return $this->getInputData()->hasAll($keys);
	}

	public function hasAny(array $keys): bool
	{
		return $this->getInputData()->hasAny($keys);
	}

	public function whenHas(string $key, callable $callback): mixed
	{
		if ($this->getInputData()->has($key)) {
			return $callback();
		}
		return null;
	}

	public function filled(string|array $keys): bool
	{
		return $this->getInputData()->filled($keys);
	}

	public function filledAny(string|array $keys): bool
	{
		return $this->getInputData()->filledAny($keys);
	}

	public function whenFilled(string $key, callable $callback): mixed
	{
		if ($this->getInputData()->filled($key)) {
			return $callback();
		}
		return null;
	}

	public function missing(string $key): bool
	{
		return $this->getInputData()->missing($key);
	}

	public function missingAny(string|array $keys): bool
	{
		return $this->getInputData()->missingAny($keys);
	}

	public function whenMissing(string $key, callable $callback): mixed
	{
		if ($this->getInputData()->missing($key)) {
			return $callback();
		}
		return null;
	}

	// -----------------

	public function string(string $key, string $default = ''): string
	{
		return $this->getInputData()->string($key, $default);
	}

	public function bool(string $key, bool $default = false): bool
	{
		return $this->getInputData()->bool($key, $default);
	}

	public function int(string $key, int $default = 0): int
	{
		return $this->getInputData()->int($key, $default);
	}

	public function float(string $key, float $default = 0.00): float
	{
		return $this->getInputData()->float($key, $default);
	}

	public function array(string $key, array $default = []): array
	{
		return $this->getInputData()->array($key, $default);
	}

	public function collection(string $key, array $default = []): Collection
	{
		return $this->getInputData()->collection($key, $default);
	}

	public function date(string $key, DateTimeZone|null $timezone = null): DateTime|null
	{
		return $this->getInputData()->date($key, $timezone);
	}

	public function moment(string $key, DateTimeZone|string|null $timezone = null): Moment|null
	{
		return $this->getInputData()->moment($key, $timezone);
	}

	public function enum(string $key, string $class, BackedEnum|null $default = null): BackedEnum|null
	{
		return $this->getInputData()->enum($key, $class, $default);
	}

	// -----------------

	public function keep(): void
	{
		Session::flashMany($this->getInputData()->all());
	}

	public function keepOnly(string|array $keys): void
	{
		Session::flashMany($this->getInputData()->only($keys));
	}

	public function keepExcept(string|array $keys): void
	{
		Session::flashMany($this->getInputData()->except($keys));
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

	public function jsonAsCollection(): Collection
	{
		return new Collection($this->jsonAsArray());
	}

	// -----------------

	protected function getInputData(): Bucket
	{
		if ($this->input_data === null) {
			$this->input_data = new Bucket(array_merge($this->query->export(), $this->post->export()));
		}
		return $this->input_data;
	}

}