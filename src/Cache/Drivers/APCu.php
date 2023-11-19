<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Drivers;

use Rovota\Core\Cache\Adapters\APCuAdapter;
use Rovota\Core\Cache\Adapters\PhpArrayAdapter;
use Rovota\Core\Cache\CacheConfig;
use Rovota\Core\Cache\CacheStore;
use Rovota\Core\Kernel\Application;

class APCu extends CacheStore
{

	public function __construct(string $name, CacheConfig $config)
	{
		if (extension_loaded('apcu') === false || Application::isEnvironment($this->config->faked_for)) {
			$adapter = new PhpArrayAdapter($config->parameters);
		} else {
			$adapter = new APCuAdapter($config->parameters);
		}

		parent::__construct($name, $adapter, $config);
	}

}