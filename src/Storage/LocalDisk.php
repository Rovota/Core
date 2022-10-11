<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Storage;

use League\Flysystem\Local\LocalFilesystemAdapter;

final class LocalDisk extends Disk
{

	public function __construct(string $name, array $options)
	{
		parent::__construct($name, new LocalFilesystemAdapter($options['root']), options: $options);
	}

}