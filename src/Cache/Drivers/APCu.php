<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Drivers;

use Rovota\Core\Cache\Adapters\APCuAdapter;
use Rovota\Core\Cache\CacheConfig;
use Rovota\Core\Cache\CacheStore;

class APCu extends CacheStore
{

	public function __construct(string $name, CacheConfig $config)
	{
		$adapter = new APCuAdapter();

		parent::__construct($name, $adapter, $config);
	}

}