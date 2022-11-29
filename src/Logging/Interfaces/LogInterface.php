<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Logging\Interfaces;

use Monolog\Logger;
use Stringable;

interface LogInterface
{

	public function when(mixed $condition, callable $callback, callable|null $alternative = null): LogInterface;

	public function unless(mixed $condition, callable $callback, callable|null $alternative = null): LogInterface;

	// -----------------

	public function isDefault(): bool;

	// -----------------

	public function name(): string;

	// -----------------

	public function option(string $name): string|int|array|null;

	public function driver(): string;

	// -----------------

	public function attach(LogInterface|string|array $channel): LogInterface;

	// -----------------

	public function log(mixed $level, string|Stringable $message, array $context = []);

	public function debug(string|Stringable $message, array $context = []);

	public function info(string|Stringable $message, array $context = []);

	public function notice(string|Stringable $message, array $context = []);

	public function warning(string|Stringable $message, array $context = []);

	public function error(string|Stringable $message, array $context = []);

	public function critical(string|Stringable $message, array $context = []);

	public function alert(string|Stringable $message, array $context = []);

	public function emergency(string|Stringable $message, array $context = []);

	// -----------------

	public function monolog(): Logger|null;

}