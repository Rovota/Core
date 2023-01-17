<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage\Drivers;

use Rovota\Core\Storage\Disk;
use Rovota\Core\Storage\DiskConfig;

final class Custom extends Disk
{

	public function __construct(string $name, DiskConfig $config)
	{
		$adapter = new $config->adapter(...$config->parameters);

		parent::__construct($name, $adapter, $config);
	}

}