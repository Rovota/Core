<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Auth;

use Rovota\Core\Support\Meta;

/**
 * @property string $role_id
 */
class RoleMeta extends Meta
{

	protected string|null $table = 'role_meta';

}