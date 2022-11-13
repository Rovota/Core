<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Views\Traits;

use Rovota\Core\Structures\Bucket;
use Rovota\Core\Views\Components\Meta;
use Rovota\Core\Views\Components\Script;
use Rovota\Core\Views\Components\Style;
use Rovota\Core\Views\View;

trait ViewModifiers
{
	/**
	 * @var array<int, Style>
	 */
	protected array $styles = [];

	/**
	 * @var array<int, Script>
	 */
	protected array $scripts = [];

	/**
	 * @var array<int, Meta>
	 */
	protected array $meta = [];

	protected Bucket|null $variables = null;

	// -----------------

	public function style(string $identifier, Style|array $attributes, bool $replace = false): View
	{
		if ($replace === true || isset($this->styles[$identifier]) === false || $attributes instanceof Style) {
			$this->styles[$identifier] = $attributes instanceof Style ? $attributes : new Style($attributes);
		} else {
			$this->styles[$identifier]->setAttributes($attributes);
		}
		return $this;
	}

	public function withoutStyle(array|string $identifiers): View
	{
		$identifiers = is_array($identifiers) ? $identifiers : [$identifiers];

		if (empty($identifiers)) {
			$this->styles = [];
		} else {
			foreach ($identifiers as $identifier) {
				unset($this->styles[$identifier]);
			}
		}
		return $this;
	}

	/**
	 * @return array<int, Style>
	 */
	public function getStyles(): array
	{
		return $this->styles;
	}

	// -----------------

	public function script(string $identifier, Script|array $attributes, bool $replace = false): View
	{
		if ($replace === true || isset($this->scripts[$identifier]) === false || $attributes instanceof Script) {
			$this->scripts[$identifier] = $attributes instanceof Script ? $attributes : new Script($attributes);
		} else {
			$this->scripts[$identifier]->setAttributes($attributes);
		}
		return $this;
	}

	public function withoutScript(array|string $identifiers): View
	{
		$identifiers = is_array($identifiers) ? $identifiers : [$identifiers];

		if (empty($identifiers)) {
			$this->scripts = [];
		} else {
			foreach ($identifiers as $identifier) {
				unset($this->scripts[$identifier]);
			}
		}
		return $this;
	}

	/**
	 * @return array<int, Script>
	 */
	public function getScripts(): array
	{
		return $this->scripts;
	}

	// -----------------

	public function meta(string $identifier, Meta|array $attributes, bool $replace = false): View
	{
		if ($replace === true || isset($this->meta[$identifier]) === false || $attributes instanceof Meta) {
			$this->meta[$identifier] = $attributes instanceof Meta ? $attributes : new Meta($attributes);
		} else {
			$this->meta[$identifier]->setAttributes($attributes);
		}
		return $this;
	}

	public function withoutMeta(array|string $identifiers): View
	{
		$identifiers = is_array($identifiers) ? $identifiers : [$identifiers];

		if (empty($identifiers)) {
			$this->meta = [];
		} else {
			foreach ($identifiers as $identifier) {
				unset($this->meta[$identifier]);
			}
		}
		return $this;
	}

	/**
	 * @return array<int, Meta>
	 */
	public function getMeta(): array
	{
		return $this->meta;
	}

	// -----------------

	public function with(array|string $name, mixed $value = null): View
	{
		if (is_array($name)) {
			foreach ($name as $key => $value) {
				$this->with($key, $value);
			}
		} else {
			if (is_array($value)) {
				foreach ($value as $key => $item) {
					$this->variables->set($name.'.'.$key, $item);
				}
			} else {
				$this->variables->set($name, $value);
			}
		}
		return $this;
	}

	public function getVariables(): Bucket
	{
		return $this->variables ?? new Bucket();
	}

}