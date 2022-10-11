<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Logging;

use Monolog\Handler\StreamHandler;

final class StreamLogger extends Logger
{

	public function __construct(string $name, array $options = [])
	{
		$handler = new StreamHandler($options['path'], $options['level']);
		parent::__construct($name, $handler, $options);
	}

}