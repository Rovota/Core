<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Logging\Drivers;

use Monolog\Handler\StreamHandler;
use Rovota\Core\Logging\Channel;
use Rovota\Core\Logging\ChannelConfig;

final class Stream extends Channel
{

	public function __construct(string $name, ChannelConfig $config)
	{
		$handler = new StreamHandler(...$config->parameters);

		parent::__construct($name, $handler, $config);
	}

}