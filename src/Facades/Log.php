<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Logging\Interfaces\ChannelInterface;
use Rovota\Core\Logging\Channel;
use Rovota\Core\Logging\LoggingManager;
use Rovota\Core\Logging\StackChannel;

final class Log
{

	protected function __construct()
	{
	}

	// -----------------

	public static function channel(string $name): ChannelInterface
	{
		return LoggingManager::get($name);
	}

	public static function stack(array $channels, string|null $name = null): ChannelInterface
	{
		return StackChannel::createUsing($channels, $name);
	}

	public static function build(array $options, string|null $name = null): ChannelInterface
	{
		return Channel::createUsing($options, $name);
	}

	// -----------------

	public static function debug(string $message, array $context = []): void
	{
		LoggingManager::get()->debug($message, $context);
	}

	public static function info(string $message, array $context = []): void
	{
		LoggingManager::get()->info($message, $context);
	}

	public static function notice(string $message, array $context = []): void
	{
		LoggingManager::get()->notice($message, $context);
	}

	public static function warning(string $message, array $context = []): void
	{
		LoggingManager::get()->warning($message, $context);
	}

	public static function error(string $message, array $context = []): void
	{
		LoggingManager::get()->error($message, $context);
	}

	public static function critical(string $message, array $context = []): void
	{
		LoggingManager::get()->critical($message, $context);
	}

	public static function alert(string $message, array $context = []): void
	{
		LoggingManager::get()->alert($message, $context);
	}

	public static function emergency(string $message, array $context = []): void
	{
		LoggingManager::get()->emergency($message, $context);
	}

}