<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage\Drivers;

use League\Flysystem\Local\LocalFilesystemAdapter;
use Rovota\Core\Storage\Disk;
use Rovota\Core\Storage\DiskConfig;

final class Local extends Disk
{

	public function __construct(string $name, DiskConfig $config)
	{
		$adapter = new LocalFilesystemAdapter($config->root);

		parent::__construct($name, $adapter, $config);
	}

}