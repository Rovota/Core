<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Localization;

use JsonSerializable;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Traits\Macroable;

class Formatter implements JsonSerializable
{
	use Macroable;

	// -----------------

	protected mixed $input = null;

	protected string|null $locale = null;
	protected Bucket|null $formats = null;

	// -----------------

	public function __construct(mixed $input, string|null $locale = null)
	{
		$this->input = $input;
		$this->formats = LocalizationManager::getFormats();

		if ($locale !== null) {
			$this->setLocaleAndFormats($locale);
		}
	}

	public function __toString(): string
	{
		return (string)$this->input;
	}

	public function jsonSerialize(): string
	{
		return (string)$this->input;
	}

	// -----------------

	public static function create(mixed $input, string|null $locale = null): static
	{
		return new static($input, $locale);
	}

	public function clone(): static
	{
		return new static($this->input, $this->locale);
	}

	// -----------------

	public function locale(string $locale): static
	{
		$this->setLocaleAndFormats($locale);
		return $this;
	}

	public function set(string $key, mixed $value): static
	{
		$this->formats->set($key, $value);
		return $this;
	}

	public function get(string $key, mixed $default = null): mixed
	{
		return $this->formats->get($key, $default);
	}

	// -----------------

	protected function setLocaleAndFormats(string $locale): void
	{
		$this->locale = $locale;
		$this->formats = LocalizationManager::getFormatsByLocale($locale);
	}

}