<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Core\Support\Traits;

use Closure;

trait Conditionable
{

	/**
	 * Executes the provided callback when the condition is `true`. Optionally, when `false`, the alternative callback will be executed.
	 */
	public function when(mixed $condition, callable $callback, callable|null $alternative = null): static
	{
		$condition = $condition instanceof Closure ? $condition($this) : $condition;

		if ($condition) {
			return $callback($this, $condition) ?? $this;
		} else {
			if ($alternative !== null) {
				return $alternative($this, $condition) ?? $this;
			}
		}
		return $this;
	}

	/**
	 * Executes the provided callback when the condition is `false`. Optionally, when `true`, the alternative callback will be executed.
	 */
	public function unless(mixed $condition, callable $callback, callable|null $alternative = null): static
	{
		$condition = $condition instanceof Closure ? $condition($this) : $condition;

		if ($condition === false) {
			return $callback($this, $condition) ?? $this;
		} else {
			if ($alternative !== null) {
				return $alternative($this, $condition) ?? $this;
			}
		}
		return $this;
	}

}