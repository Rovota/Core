<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Storage\Drivers;

use Rovota\Core\Storage\Disk;

final class Custom extends Disk
{

	public function __construct(string $name, array $options = [])
	{
		$adapter = new $options['adapter'](...$options['parameters']);
		parent::__construct($name, $adapter, $options);
	}

	// -----------------

	public function parameter(string $name): string|int|array|null
	{
		return $this->options['parameters'][$name] ?? null;
	}

}