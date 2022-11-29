<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage;

use Rovota\Core\Support\Meta;

/**
 * @property string $media_id
 */
class MediaMeta extends Meta
{

	protected string|null $table = 'media_meta';

}