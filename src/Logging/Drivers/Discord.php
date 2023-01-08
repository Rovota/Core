<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Logging\Drivers;

use Rovota\Core\Logging\ChannelConfig;
use Rovota\Core\Logging\Handlers\DiscordHandler;
use Rovota\Core\Logging\Channel;

final class Discord extends Channel
{

	public function __construct(string $name, ChannelConfig $config)
	{
		$handler = new DiscordHandler(...$config->parameters);

		parent::__construct($name, $handler, $config);
	}

}