<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Drivers;

use Exception;
use Rovota\Core\Cache\Adapters\PhpArrayAdapter;
use Rovota\Core\Cache\Adapters\RedisAdapter;
use Rovota\Core\Cache\CacheConfig;
use Rovota\Core\Cache\CacheStore;
use Rovota\Core\Kernel\Application;

class Redis extends CacheStore
{

	public function __construct(string $name, CacheConfig $config)
	{
		if (extension_loaded('redis') === false || Application::isEnvironment($this->config->faked_for)) {
			$adapter = new PhpArrayAdapter();
		} else {
			try {
				$adapter = new RedisAdapter($config->parameters);
			} catch (Exception) {
				$adapter = new PhpArrayAdapter();
			}
		}

		parent::__construct($name, $adapter, $config);
	}

}