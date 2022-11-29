<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Logging\Drivers;

use Rovota\Core\Logging\Handlers\DiscordHandler;
use Rovota\Core\Logging\Logger;

final class Discord extends Logger
{

	public function __construct(string $name, array $options = [])
	{
		$handler = new DiscordHandler($options['token'], $options['channel'], $options['level']);
		parent::__construct($name, $handler, $options);
	}

}