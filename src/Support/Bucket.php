<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support;

use ArrayAccess;
use Dflydev\DotAccessData\Data;
use JsonSerializable;
use Rovota\Core\Support\Traits\BucketAccessors;

class Bucket implements ArrayAccess, JsonSerializable
{
	use BucketAccessors;

	public const PRESERVE = 0;
	public const REPLACE = 1;
	public const MERGE = 2;

	protected Data $data;

	// -----------------

	public function __construct(array $data = [])
	{
		$this->data = new Data($data);
	}

	// -----------------

	public function jsonSerialize(): array
	{
		return $this->data->export();
	}

	// -----------------

	public function append(string $key, mixed $value = null): static
	{
		$this->data->append($key, $value);
		return $this;
	}

	public function set(string $key, mixed $value = null): static
	{
		$this->data->set($key, $value);
		return $this;
	}

	public function remove(string $key): static
	{
		$this->data->remove($key);
		return $this;
	}

	public function get(string $key, mixed $default = null): mixed
	{
		return $this->data->get($key, $default);
	}

	public function has(string $key): bool
	{
		return $this->data->has($key);
	}

	// -----------------

	public function flush(): void
	{
		$this->data = new Data([]);
	}

	public function count(string|null $key): int
	{
		$value = $key === null ? $this->export() : $this->get($key);
		return is_array($value) ? count($value) : 1;
	}

	// -----------------

	public function hasAll(array $keys): bool
	{
		foreach ($keys as $key) {
			if ($this->has($key) === false) {
				return false;
			}
		}
		return true;
	}

	public function hasAny(array $keys): bool
	{
		foreach ($keys as $key) {
			if ($this->has($key) === true) {
				return true;
			}
		}
		return false;
	}

	public function filled(string|array $keys): bool
	{
		foreach (is_array($keys) ? $keys : [$keys] as $key) {
			if ($this->get($key) === null) {
				return false;
			}
		}
		return true;
	}

	public function filledAny(array $keys): bool
	{
		foreach ($keys as $key) {
			if ($this->get($key) !== null) {
				return true;
			}
		}
		return false;
	}

	public function missing(string $key): bool
	{
		return $this->has($key) === false;
	}

	public function missingAny(string|array $keys): bool
	{
		foreach (is_array($keys) ? $keys : [$keys] as $key) {
			if ($this->has($key) === false) {
				return true;
			}
		}
		return false;
	}

	// -----------------

	public function export(): array
	{
		return $this->data->export();
	}

	public function import(array $data, int $mode = self::REPLACE): static
	{
		$this->data->import($data, $mode);
		return $this;
	}

	public function replace(array $data): static
	{
		$this->data->import($data, self::REPLACE);
		return $this;
	}

	public function merge(array $data): static
	{
		$this->data->import($data, self::MERGE);
		return $this;
	}

	public function mergeIfMissing(array $data): static
	{
		$this->data->import($data, self::PRESERVE);
		return $this;
	}

	// -----------------

	public function toJson(): string
	{
		return json_encode_clean($this->jsonSerialize());
	}

	public function toArray(): array
	{
		return $this->data->export();
	}

	// -----------------

	public function offsetExists(mixed $offset): bool
	{
		return $this->has($offset);
	}

	public function offsetGet(mixed $offset): mixed
	{
		return $this->get($offset);
	}

	public function offsetSet(mixed $offset, mixed $value): void
	{
		$this->set($offset, $value);
	}

	public function offsetUnset(mixed $offset): void
	{
		$this->remove($offset);
	}

}