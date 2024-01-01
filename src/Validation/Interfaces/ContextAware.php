<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Interfaces;

use Rovota\Core\Structures\Bucket;

interface ContextAware
{
	public function withContext(array $data): static;

	public function getContext(): Bucket;

}