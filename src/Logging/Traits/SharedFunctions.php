<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 *
 * Inspired by the Laravel/Conditionable trait.
 */

namespace Rovota\Core\Logging\Traits;

use Closure;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Logging\Drivers\Stack;
use Rovota\Core\Logging\Interfaces\LogInterface;
use Rovota\Core\Logging\LoggingManager;
use Throwable;

trait SharedFunctions
{

	public function when(mixed $condition, callable $callback, callable|null $alternative = null): LogInterface
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

	public function unless(mixed $condition, callable $callback, callable|null $alternative = null): LogInterface
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

	// -----------------

	public function __toString(): string
	{
		return $this->name;
	}

	public function __get(string $name): mixed
	{
		return $this->options[$name] ?? null;
	}

	public function __isset(string $name): bool
	{
		return isset($this->options[$name]);
	}

	// -----------------

	public function isDefault(): bool
	{
		return LoggingManager::getDefault() === $this->name;
	}

	// -----------------

	public function name(): string
	{
		return $this->name;
	}

	// -----------------

	public function option(string $name): string|int|array|null
	{
		return $this->options[$name] ?? null;
	}

	public function driver(): string
	{
		return $this->option('driver');
	}

	// -----------------

	public function attach(LogInterface|string|array $channel): LogInterface
	{
		if ($this instanceof Stack) {
			// It already is a stack. Just add the given channel(s).
			$channels = is_array($channel) ? $channel : [$channel];
			foreach ($channels as $channel) {
				$this->options['channels'][] = $channel;
			}
			return $this;
		}

		try {
			// Create an on-demand stack using the current and new channel(s).
			return Stack::createUsing([$this])->attach($channel);
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
			return $this;
		}
	}

}