<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Logging;

use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Logging\Interfaces\ChannelInterface;
use Rovota\Core\Support\Str;
use Rovota\Core\Support\Traits\Conditionable;
use Stringable;
use Throwable;

abstract class Channel implements ChannelInterface
{
	use Conditionable;

	protected string $name;

	protected ChannelConfig $config;

	protected HandlerInterface $handler;

	protected Logger $logger;

	// -----------------

	public function __construct(string $name, HandlerInterface $handler, ChannelConfig $config)
	{
		$this->name = $name;
		$this->config = $config;

		$this->handler = $handler;
		$this->logger = new Logger($name);
		$this->logger->pushHandler($handler);
		$this->logger->pushProcessor(new PsrLogMessageProcessor());
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

	public static function createUsing(array $options, string|null $name = null): ChannelInterface
	{
		return LoggingManager::build($name ?? Str::random(20), $options);
	}

	// -----------------

	public function log(mixed $level, string|Stringable $message, array $context = []): void
	{
		$this->monolog->log($level, $message, $context);
	}

	public function debug(string|Stringable $message, array $context = []): void
	{
		$this->monolog->debug($message, $context);
	}

	public function info(string|Stringable $message, array $context = []): void
	{
		$this->monolog->info($message, $context);
	}

	public function notice(string|Stringable $message, array $context = []): void
	{
		$this->monolog->notice($message, $context);
	}

	public function warning(string|Stringable $message, array $context = []): void
	{
		$this->monolog->warning($message, $context);
	}

	public function error(string|Stringable $message, array $context = []): void
	{
		$this->monolog->error($message, $context);
	}

	public function critical(string|Stringable $message, array $context = []): void
	{
		$this->monolog->critical($message, $context);
	}

	public function alert(string|Stringable $message, array $context = []): void
	{
		$this->monolog->alert($message, $context);
	}

	public function emergency(string|Stringable $message, array $context = []): void
	{
		$this->monolog->emergency($message, $context);
	}

	// -----------------

	public function attach(ChannelInterface|string|array $channel): ChannelInterface
	{
		try {
			// Create an on-demand stack using the current and new channel(s).
			return StackChannel::createUsing([$this])->attach($channel);
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
			return $this;
		}
	}

	// -----------------

	public function monolog(): Logger|null
	{
		return $this->logger;
	}

}