<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Logging\Interfaces;

use Monolog\Logger;
use Rovota\Core\Logging\ChannelConfig;
use Stringable;

interface ChannelInterface
{

	public function isDefault(): bool;

	// -----------------

	public function name(): string;

	public function config(): ChannelConfig;

	// -----------------

	public function attach(ChannelInterface|string|array $channel): ChannelInterface;

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