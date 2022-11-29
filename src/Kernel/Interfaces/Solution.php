<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Kernel\Interfaces;

interface Solution
{

	public function getTitle(): string;

	public function getDescription(): string;

	public function getDocumentationLinks(): array;

}