<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Logging\Drivers;

use Monolog\Handler\StreamHandler;
use Rovota\Core\Logging\Logger;

final class Stream extends Logger
{

	public function __construct(string $name, array $options = [])
	{
		$handler = new StreamHandler($options['path'], $options['level']);
		parent::__construct($name, $handler, $options);
	}

}