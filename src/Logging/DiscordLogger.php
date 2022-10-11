<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Logging;

use Rovota\Core\Logging\Handlers\DiscordHandler;

final class DiscordLogger extends Logger
{

	public function __construct(string $name, array $options = [])
	{
		$handler = new DiscordHandler($options['token'], $options['channel'], $options['level']);
		parent::__construct($name, $handler, $options);
	}

}