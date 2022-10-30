<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Storage\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum Driver: string
{
	use EnumHelpers;

	case AwsS3 = 's3';
	case Custom = 'custom';
	case Local = 'local';
	case Sftp = 'sftp';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Driver::AwsS3 => 'AWS S3',
			Driver::Custom => 'Custom',
			Driver::Local => 'Filesystem',
			Driver::Sftp => 'SFTP',
		};
	}

}