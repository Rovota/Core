<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
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

	public function parameter(string $name): mixed
	{
		return $this->options['parameters'][$name] ?? null;
	}

}