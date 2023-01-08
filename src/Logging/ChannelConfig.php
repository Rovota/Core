<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Logging;

use Monolog\Level;
use Rovota\Core\Logging\Enums\Driver;
use Rovota\Core\Support\Config;

/**
 * @property Driver|null $driver
 * @property string $label
 * @property array|null $channels
 * @property string|null $handler
 * @property Config $parameters
 * @property bool $auto_connect
 * @property bool $read_only
 */
final class ChannelConfig extends Config
{

	protected function driver(): Driver|null
	{
		return Driver::tryFrom($this->get('driver', '-'));
	}

	protected function label(): string
	{
		return $this->get('label', 'Unnamed Channel');
	}

	// -----------------

	protected function channels(): array|null
	{
		return $this->get('channels');
	}

	protected function handler(): string|null
	{
		return $this->get('handler');
	}

	protected function parameters(): Config
	{
		return new Config($this->array('parameters'));
	}

	// -----------------

	public function isValid(): bool
	{
		return true;
	}

}