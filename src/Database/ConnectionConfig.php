<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database;

use Rovota\Core\Database\Enums\Driver;
use Rovota\Core\Support\Config;

/**
 * @property Driver|null $driver
 * @property string $label
 * @property Config $parameters
 * @property bool $auto_connect
 */
final class ConnectionConfig extends Config
{

	protected function driver(): Driver|null
	{
		return Driver::tryFrom($this->get('driver', '-'));
	}

	protected function label(): string
	{
		return $this->get('label', 'Unnamed Database');
	}

	// -----------------

	protected function parameters(): Config
	{
		return new Config($this->array('parameters'));
	}

	// -----------------

	protected function autoConnect(): bool
	{
		return $this->bool('auto_connect');
	}

	// -----------------

	public function isValid(): bool
	{
		return true;
	}

}