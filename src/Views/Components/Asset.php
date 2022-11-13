<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Views\Components;

use Rovota\Core\Support\Text;
use Rovota\Core\Support\Traits\Conditionable;

class Asset
{
	use Conditionable;

	protected array $attributes = [];

	// -----------------

	public function __construct(array $attributes = [])
	{
		$this->setAttributes($attributes);
	}

	// -----------------

	public function hasAttribute(string $name): bool
	{
		return isset($this->attributes[$name]) || $this->attributes[$name] === null;
	}

	public function setAttributes(array $attributes = []): void
	{
		if (empty($attributes) === false) {
			foreach ($attributes as $name => $value) {
				$this->setAttribute($name, $value);
			}
		}
	}

	public function setAttribute(string $name, string|null $value): void
	{
		if ($value === null) {
			unset($this->attributes[$name]);
		}
		if (Text::length($name) > 0 && Text::length($value) > 0) {
			$this->attributes[$name] = $value;
		}
	}

	public function getAttributes(array $names = [], bool $filter_missing = true): array
	{
		if (empty($names)) {
			return $this->attributes;
		}

		$result = [];
		foreach ($names as $name) {
			if ($filter_missing && $this->hasAttribute($name) === false) {
				continue;
			}
			$result[$name] = $this->getAttribute($name);
		}
		return $result;
	}

	public function getAttribute(string $name): string
	{
		return $this->attributes[$name] ?? '';
	}

}