<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Kernel;

use Rovota\Core\Convert\ConversionManager;
use Rovota\Core\Facades\Format;
use Rovota\Core\Support\Str;
use const PHP_INT_MAX;

final class Server
{

	private int|float $space_total;
	private int|float $space_free;
	private int|float $space_used;

	private array $variables;

	// -----------------

	public function __construct()
	{
		// Process server headers
		$this->variables = array_change_key_case($_SERVER);

		// Gather server statistics
		$disk = ($this->os() === 'Windows') ? substr(getcwd(), 0, 3) : '/';
		$this->space_total = disk_total_space($disk) ?: 0;
		$this->space_free = disk_free_space($disk) ?: 0;
		$this->space_used = $this->space_total - $this->space_free;
	}

	// -----------------

	public function os(): string
	{
		return trim(str_replace('NT', '', php_uname('s')));
	}

	public function release(): string
	{
		return php_uname('r');
	}

	public function version(): string
	{
		return php_uname('v');
	}

	public function name(): string
	{
		return php_uname('n');
	}

	public function platform(): string
	{
		return php_uname('m');
	}

	public function zendVersion(): string
	{
		return zend_version();
	}

	public function phpVersion(): string
	{
		return PHP_VERSION;
	}

	public function extVersion(string $extension): string
	{
		return phpversion($extension);
	}

	public function extLoaded(string $extension): bool
	{
		return extension_loaded($extension);
	}

	// -----------------

	public function maxFilesize(): int
	{
		$post_max = ConversionManager::textToBytes(ini_get('post_max_size'));
		$upload_max = ConversionManager::textToBytes(ini_get('upload_max_filesize'));

		return min($post_max ?: PHP_INT_MAX, $upload_max ?: PHP_INT_MAX);
	}

	// -----------------

	public function has(string $name): bool
	{
		return isset($this->variables[$name]);
	}

	public function get(string $name, string $default = ''): string
	{
		$name = Str::lower($name);
		return $this->variables[$name] ?? $default;
	}

	public function diskSize(bool $format = false): int|array
	{
		return $format ? Format::asCapacity($this->space_total) : $this->space_total;
	}

	public function diskSpace(bool $format = false): int|array
	{
		return $format ? Format::asCapacity($this->space_free) : $this->space_free;
	}

	public function diskUsage(bool $format = false): int|array
	{
		return $format ? Format::asCapacity($this->space_used) : $this->space_used;
	}

	public function memoryAllocated(bool $format = false): int|array
	{
		return $format ? Format::asCapacity(memory_get_usage(true)) : memory_get_usage(true);
	}

	public function memoryUsage(bool $format = false): int|array
	{
		return $format ? Format::asCapacity(memory_get_usage()) : memory_get_usage();
	}

	public function memoryPeakUsage(bool $format = false): int|array
	{
		return $format ? Format::asCapacity(memory_get_peak_usage(true)) : (float)memory_get_peak_usage(true);
	}

}