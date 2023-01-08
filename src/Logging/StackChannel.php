<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Logging;

use Monolog\Logger;
use Rovota\Core\Logging\Interfaces\ChannelInterface;
use Rovota\Core\Support\Str;
use Rovota\Core\Support\Traits\Conditionable;
use Stringable;

class StackChannel implements ChannelInterface
{
	use Conditionable;

	protected string $name;

	protected ChannelConfig $config;

	// -----------------

	public function __construct(string $name, ChannelConfig $config)
	{
		$this->name = $name;
		$this->config = $config;
	}

	// -----------------

	public function __toString(): string
	{
		return $this->name;
	}

	public function __get(string $name): mixed
	{
		return $this->config->get($name);
	}

	public function __isset(string $name): bool
	{
		return $this->config->has($name);
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

	public function config(): ChannelConfig
	{
		return $this->config;
	}

	// -----------------

	public static function createUsing(array $channels, string|null $name = null): ChannelInterface
	{
		return LoggingManager::build($name ?? Str::random(20), [
			'driver' => 'stack',
			'Unnamed Channel',
			'channels' => $channels,
		]);
	}

	// -----------------

	public function log(mixed $level, string|Stringable $message, array $context = []): void
	{
		foreach ($this->config->channels as $channel) {
			if ($channel instanceof ChannelInterface) {
				$channel->log($level, $message, $context); continue;
			}
			LoggingManager::get($channel)->log($level, $message, $context);
		}
	}

	public function debug(string|Stringable $message, array $context = []): void
	{
		foreach ($this->config->channels as $channel) {
			if ($channel instanceof ChannelInterface) {
				$channel->debug($message, $context); continue;
			}
			LoggingManager::get($channel)->debug($message, $context);
		}
	}

	public function info(string|Stringable $message, array $context = []): void
	{
		foreach ($this->config->channels as $channel) {
			if ($channel instanceof ChannelInterface) {
				$channel->info($message, $context); continue;
			}
			LoggingManager::get($channel)->info($message, $context);
		}
	}

	public function notice(string|Stringable $message, array $context = []): void
	{
		foreach ($this->config->channels as $channel) {
			if ($channel instanceof ChannelInterface) {
				$channel->notice($message, $context); continue;
			}
			LoggingManager::get($channel)->notice($message, $context);
		}
	}

	public function warning(string|Stringable $message, array $context = []): void
	{
		foreach ($this->config->channels as $channel) {
			if ($channel instanceof ChannelInterface) {
				$channel->warning($message, $context); continue;
			}
			LoggingManager::get($channel)->warning($message, $context);
		}
	}

	public function error(string|Stringable $message, array $context = []): void
	{
		foreach ($this->config->channels as $channel) {
			if ($channel instanceof ChannelInterface) {
				$channel->error($message, $context); continue;
			}
			LoggingManager::get($channel)->error($message, $context);
		}
	}

	public function critical(string|Stringable $message, array $context = []): void
	{
		foreach ($this->config->channels as $channel) {
			if ($channel instanceof ChannelInterface) {
				$channel->critical($message, $context); continue;
			}
			LoggingManager::get($channel)->critical($message, $context);
		}
	}

	public function alert(string|Stringable $message, array $context = []): void
	{
		foreach ($this->config->channels as $channel) {
			if ($channel instanceof ChannelInterface) {
				$channel->alert($message, $context); continue;
			}
			LoggingManager::get($channel)->alert($message, $context);
		}
	}

	public function emergency(string|Stringable $message, array $context = []): void
	{
		foreach ($this->config->channels as $channel) {
			if ($channel instanceof ChannelInterface) {
				$channel->emergency($message, $context); continue;
			}
			LoggingManager::get($channel)->emergency($message, $context);
		}
	}

	// -----------------

	public function attach(ChannelInterface|string|array $channel): ChannelInterface
	{
		$current = $this->config->channels;
		$new = is_array($channel) ? $channel : [$channel];

		$this->config->set('channels', array_merge($current, $new));
		return $this;
	}

	// -----------------

	public function monolog(): Logger|null
	{
		return null;
	}

}