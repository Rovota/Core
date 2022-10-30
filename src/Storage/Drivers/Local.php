<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Storage\Drivers;

use League\Flysystem\Local\LocalFilesystemAdapter;
use Rovota\Core\Storage\Disk;

final class Local extends Disk
{

	public function __construct(string $name, array $options)
	{
		parent::__construct($name, new LocalFilesystemAdapter($options['root']), options: $options);
	}

}