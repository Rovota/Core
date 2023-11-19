<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Drivers;

use Rovota\Core\Cache\Adapters\PhpArrayAdapter;
use Rovota\Core\Cache\CacheConfig;
use Rovota\Core\Cache\CacheStore;

class PhpArray extends CacheStore
{

	public function __construct(string $name, CacheConfig $config)
	{
		$adapter = new PhpArrayAdapter($config->parameters);

		parent::__construct($name, $adapter, $config);
	}

}