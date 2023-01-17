<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Cache\Drivers;

use RedisException;
use Rovota\Core\Cache\Adapters\RedisAdapter;
use Rovota\Core\Cache\CacheConfig;
use Rovota\Core\Cache\CacheStore;

class Redis extends CacheStore
{

	/**
	 * @throws RedisException
	 */
	public function __construct(string $name, CacheConfig $config)
	{
		$adapter = new RedisAdapter($config->parameters);

		parent::__construct($name, $adapter, $config);
	}

}