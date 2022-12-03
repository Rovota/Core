<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage;

use Rovota\Core\Storage\Enums\Driver;
use Rovota\Core\Support\Config;

/**
 * @property Driver|null $driver
 * @property string $label
 * @property string $root
 * @property string|array|null $domain
 * @property string|null $adapter
 * @property Config $parameters
 * @property bool $auto_connect
 * @property bool $read_only
 */
final class DiskConfig extends Config
{

	protected function driver(): Driver|null
	{
		return Driver::tryFrom($this->get('driver', '-'));
	}

	protected function label(): string
	{
		return $this->get('label', 'Unnamed Disk');
	}

	// -----------------

	protected function root(): string
	{
		return $this->get('root', '');
	}

	protected function domain(): string|array|null
	{
		return $this->get('domain');
	}

	protected function adapter(): string|null
	{
		return $this->get('adapter');
	}

	protected function parameters(): Config
	{
		return new Config($this->array('parameters'));
	}

	// -----------------

	protected function autoConnect(): bool
	{
		return $this->bool('auto_connect');
	}

	protected function readOnly(): bool
	{
		return $this->bool('read_only');
	}

	// -----------------

	public function isValid(): bool
	{
		return true;
	}

}