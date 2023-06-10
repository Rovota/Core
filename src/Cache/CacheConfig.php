<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache;

use Rovota\Core\Cache\Enums\Driver;
use Rovota\Core\Support\Config;

/**
 * @property Driver|null $driver
 * @property string $label
 * @property int $retention
 * @property array $faked_for
 * @property array $disabled_for
 * @property Config $parameters
 * @property bool $auto_connect
 */
final class CacheConfig extends Config
{

	protected function driver(): Driver|null
	{
		return Driver::tryFrom($this->get('driver', '-'));
	}

	protected function label(): string
	{
		return $this->get('label', 'Unnamed Cache');
	}

	// -----------------

	protected function retention(): int
	{
		return $this->int('retention', 60);
	}

	protected function fakedFor(): array
	{
		return $this->array('faked_for');
	}

	protected function disabledFor(): array
	{
		return $this->array('disabled_for');
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

	// -----------------

	public function isValid(): bool
	{
		return true;
	}

}