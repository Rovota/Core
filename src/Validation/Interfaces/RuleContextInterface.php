<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Interfaces;

interface RuleContextInterface
{

	public function setContext(array $data): void;

}