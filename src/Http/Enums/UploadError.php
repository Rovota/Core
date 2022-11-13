<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum UploadError: int
{
	use EnumHelpers;

	case ExceedsIniSize = 1;
	case ExceedsFormSize = 2;
	case Partial = 3;
	case NoFile = 4;
	case CannotWrite = 7;
	case NoTempDir = 6;
	case Extension = 8;

	// -----------------

	public function label(): string
	{
		return match ($this) {
			UploadError::ExceedsIniSize => 'Exceeds Ini Size',
			UploadError::ExceedsFormSize => 'Exceeds Form Size',
			UploadError::Partial => 'Partial Upload',
			UploadError::NoFile => 'No File',
			UploadError::CannotWrite => 'Cannot Write',
			UploadError::NoTempDir => 'No Temporary Directory',
			UploadError::Extension => 'Extension',
		};
	}

	public function message(): string
	{
		// Messages from Symfony/Component/HttpFoundation/File/UploadedFile.php.
		return match ($this) {
			UploadError::ExceedsIniSize => 'The file "%s" exceeds your upload_max_filesize ini directive.',
			UploadError::ExceedsFormSize => 'The file "%s" exceeds the upload limit defined in your form.',
			UploadError::Partial => 'The file "%s" was only partially uploaded.',
			UploadError::NoFile => 'No file was uploaded.',
			UploadError::CannotWrite => 'The file "%s" could not be written to disk.',
			UploadError::NoTempDir => 'File could not be uploaded: missing temporary directory.',
			UploadError::Extension => 'File upload was stopped by a PHP extension.',
		};
	}

}