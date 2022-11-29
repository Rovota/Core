<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http\Client;

use Rovota\Core\Http\Client\Traits\PasswordService;

final class HibpClient extends Client
{
	use PasswordService;
}