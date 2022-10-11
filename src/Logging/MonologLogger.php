<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Logging;

final class MonologLogger extends Logger
{

	public function __construct(string $name, array $options = [])
	{
		$handler = new $options['handler'](...$options['parameters']);
		parent::__construct($name, $handler, $options);
	}

}