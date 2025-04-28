<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Auth\Interfaces;

interface MailSupportsCode
{

	public function code(string $code): static;

}