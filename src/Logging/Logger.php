<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Logging;

use Monolog\Handler\HandlerInterface;
use Monolog\Logger as MonoLogger;
use Monolog\Processor\PsrLogMessageProcessor;
use Rovota\Core\Logging\Interfaces\LogInterface;
use Rovota\Core\Logging\Traits\SharedFunctions;
use Rovota\Core\Support\Text;
use Stringable;

abstract class Logger implements LogInterface
{
	use SharedFunctions;

	protected string $name;

	protected array $options;

	protected MonoLogger $monolog;

	// -----------------

	public function __construct(string $name, HandlerInterface $handler, array $options = [])
	{
		$this->name = $name;
		$this->options = $options;

		$this->monolog = new MonoLogger($name);
		$this->monolog->pushHandler($handler);
		$this->monolog->pushProcessor(new PsrLogMessageProcessor());
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Logging\Exceptions\UnsupportedDriverException
	 */
	public static function createUsing(array $options, string|null $name = null): LogInterface
	{
		return LoggingManager::build($name ?? Text::random(20), $options);
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

	public function monolog(): MonoLogger|null
	{
		return $this->monolog;
	}

}