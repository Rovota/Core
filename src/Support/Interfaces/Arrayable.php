<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support\Interfaces;

interface Arrayable
{

	/**
	 * Returns an array representation of the instance.
	 */
	public function toArray(): array;

}