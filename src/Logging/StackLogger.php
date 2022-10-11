<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Logging;

use Monolog\Logger;
use Rovota\Core\Logging\Interfaces\LogInterface;
use Rovota\Core\Logging\Traits\SharedFunctions;
use Rovota\Core\Support\Text;
use Stringable;

final class StackLogger implements LogInterface
{
	use SharedFunctions;

	protected string $name;

	protected array $options;

	// -----------------

	public function __construct(string $name, array $options = [])
	{
		$this->name = $name;
		$this->options = $options;
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Logging\Exceptions\UnsupportedDriverException
	 */
	public static function createUsing(array $channels): LogInterface
	{
		return LoggingManager::build(Text::random(20), [
			'driver' => 'stack',
			'channels' => $channels,
		]);
	}

	// -----------------

	public function log(mixed $level, string|Stringable $message, array $context = []): void
	{
		foreach ($this->options['channels'] as $channel) {
			if ($channel instanceof LogInterface) {
				$channel->log($level, $message, $context); continue;
			}
			LoggingManager::get($channel)->log($level, $message, $context);
		}
	}

	public function debug(string|Stringable $message, array $context = []): void
	{
		foreach ($this->options['channels'] as $channel) {
			if ($channel instanceof LogInterface) {
				$channel->debug($message, $context); continue;
			}
			LoggingManager::get($channel)->debug($message, $context);
		}
	}

	public function info(string|Stringable $message, array $context = []): void
	{
		foreach ($this->options['channels'] as $channel) {
			if ($channel instanceof LogInterface) {
				$channel->info($message, $context); continue;
			}
			LoggingManager::get($channel)->info($message, $context);
		}
	}

	public function notice(string|Stringable $message, array $context = []): void
	{
		foreach ($this->options['channels'] as $channel) {
			if ($channel instanceof LogInterface) {
				$channel->notice($message, $context); continue;
			}
			LoggingManager::get($channel)->notice($message, $context);
		}
	}

	public function warning(string|Stringable $message, array $context = []): void
	{
		foreach ($this->options['channels'] as $channel) {
			if ($channel instanceof LogInterface) {
				$channel->warning($message, $context); continue;
			}
			LoggingManager::get($channel)->warning($message, $context);
		}
	}

	public function error(string|Stringable $message, array $context = []): void
	{
		foreach ($this->options['channels'] as $channel) {
			if ($channel instanceof LogInterface) {
				$channel->error($message, $context); continue;
			}
			LoggingManager::get($channel)->error($message, $context);
		}
	}

	public function critical(string|Stringable $message, array $context = []): void
	{
		foreach ($this->options['channels'] as $channel) {
			if ($channel instanceof LogInterface) {
				$channel->critical($message, $context); continue;
			}
			LoggingManager::get($channel)->critical($message, $context);
		}
	}

	public function alert(string|Stringable $message, array $context = []): void
	{
		foreach ($this->options['channels'] as $channel) {
			if ($channel instanceof LogInterface) {
				$channel->alert($message, $context); continue;
			}
			LoggingManager::get($channel)->alert($message, $context);
		}
	}

	public function emergency(string|Stringable $message, array $context = []): void
	{
		foreach ($this->options['channels'] as $channel) {
			if ($channel instanceof LogInterface) {
				$channel->emergency($message, $context); continue;
			}
			LoggingManager::get($channel)->emergency($message, $context);
		}
	}

	// -----------------

	public function monolog(): Logger|null
	{
		return null;
	}

}