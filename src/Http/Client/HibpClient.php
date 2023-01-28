<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http\Client;

use Rovota\Core\Http\Client\Traits\HibpPasswordService;

final class HibpClient extends Client
{
	use HibpPasswordService;
}