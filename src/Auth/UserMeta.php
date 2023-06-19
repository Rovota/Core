<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Auth;

use Rovota\Core\Support\Meta;

/**
 * @property string $user_id
 */
class UserMeta extends Meta
{

	protected string|null $table = '_user_meta';

}