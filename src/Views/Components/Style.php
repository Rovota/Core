<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Views\Components;

class Style extends Asset
{

	protected array $attributes = [
		'rel' => 'stylesheet'
	];

	protected string|null $cache_key = null;
	protected string|null $version = null;

	// -----------------

	public function cacheKey(string $key): static
	{
		$this->cache_key = $key;
		return $this;
	}

	public function version(string $value): static
	{
		$this->version = $value;
		return $this;
	}

	// -----------------

	public function rel(string $value): static
	{
		$this->setAttribute('rel', $value);
		return $this;
	}

	public function source(string $url): static
	{
		$this->setAttribute('href', $url);
		return $this;
	}

	public function media(string $value): static
	{
		$this->setAttribute('media', $value);
		return $this;
	}

	// -----------------

	public function __toString(): string
	{
		return $this->formatHtml();
	}

	// -----------------

	public function formatHtml(): string
	{
		$attributes = '';
		foreach ($this->getAttributes() as $name => $value) {
			if ($name === 'href' && $this->cache_key !== null) {
				$value = match(true) {
					str_contains($value, ':cache_key') => str_replace(':cache_key', $this->cache_key, $value),
					default => $value.'?cache_key='.$this->cache_key,
				};
			}
			if ($name === 'href' && $this->version !== null) {
				$value = str_replace(':version', $this->version, $value);
			}
			$attributes .= sprintf(' %s="%s"', $name, $value);
		}

		return sprintf('<link %s>', trim($attributes)).PHP_EOL;
	}

}