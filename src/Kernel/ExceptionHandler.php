<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Kernel;

use Monolog\Level;
use Rovota\Core\Kernel\Interfaces\ProvidesSolution;
use Rovota\Core\Logging\LoggingManager;
use Stringable;
use Throwable;

final class ExceptionHandler
{

	private static bool $debug_enabled;
	private static bool $log_enabled;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 */
	public static function initialize(): void
	{
		self::$debug_enabled = getenv('ENABLE_DEBUG') === 'true';
		self::$log_enabled = getenv('ENABLE_LOGGING') === 'true';
	}

	// -----------------

	public static function addThrowable(Throwable $throwable, bool $unhandled = false): void
	{
		self::logThrowable($throwable, $unhandled);

		if (self::$debug_enabled) {
			self::renderDebug($throwable, $unhandled);
		} else {
			ob_clean();
			http_response_code(500);
		}
	}

	public static function addError(int $number, string $message, string $file, int $line): void
	{
		self::logError($number, $message, $file, $line);

		if (self::$debug_enabled) {
			echo $message;
		}
	}

	// -----------------

	/** @noinspection PhpUnusedParameterInspection */
	public static function renderDebug(Throwable $throwable, bool $unhandled = false): void
	{
		ob_clean();

		$request = self::getRequestInfo();
		$snippet = self::getSnippet($throwable);
		$solution = $throwable instanceof ProvidesSolution ? $throwable->getSolution() : null;
		$traces = self::getFilteredTrace($throwable);

		include base_path('vendor/rovota/core/src/Web/views/debug.php');
		exit;
	}

	// -----------------

	public static function logThrowable(Throwable $throwable, bool $unhandled = false): void
	{
		if (self::$log_enabled) {
			LoggingManager::get()->log($unhandled ? Level::Critical : Level::Warning, $throwable->getMessage(), [
				$throwable::class, $throwable->getFile(), $throwable->getLine(), self::getRequestInfo()
			]);
		}
	}

	public static function logMessage(mixed $level, string|Stringable $message, array $context = []): void
	{
		if (self::$log_enabled) {
			LoggingManager::get()->log($level, $message, $context);
		}
	}

	public static function logError(int $number, string $message, string $file, int $line): void
	{
		if (self::$log_enabled) {
			LoggingManager::get()->error($message, [$number, $file, $line]);
		}
	}

	// -----------------

	protected static function getRequestInfo(): array
	{
		return ['full_url' => $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'],];
	}

	protected static function getFilteredTrace(Throwable $throwable): array
	{
		$filtered = [];

		$filtered[0] = ['line' => $throwable->getLine(), 'function' => '', 'file' => trim(str_replace(dirname(str_replace('/', '\\', $_SERVER['SCRIPT_FILENAME']), 2), '', $throwable->getFile()), '\\'), 'class' => null, 'type' => '',];

		foreach ($throwable->getTrace() as $key => $trace) {
			$filtered[$key + 1] = [
				'line' => $trace['line'] ?? '#',
				'function' => $trace['function'],
				'file' => trim(str_replace(dirname(str_replace('/', '\\', $_SERVER['SCRIPT_FILENAME']), 2), '', $trace['file'] ?? ''), '\\'),
				'class' => $trace['class'] ?? null,
				'type' => match ($trace['type'] ?? null) {
				'::' => '<badge class="static">Static</badge>',
				'->' => '<badge class="non-static">Non-Static</badge>',
				default => '',
			},];
		}

		return $filtered;
	}

	protected static function getSnippet(Throwable $throwable): array
	{
		try {
			$content = file($throwable->getFile(), FILE_IGNORE_NEW_LINES);
			if ($content === false) {
				return [];
			}
		} catch (Throwable) {
			return [];
		}

		return $content;
	}

}