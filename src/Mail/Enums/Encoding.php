<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Mail\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum Encoding: string
{
	use EnumHelpers;

	case Base64 = 'base64';
	case UTF7 = '7bit';
	case UTF8 = '8bit';
	case Binary = 'binary';
	case QuotedPrintable = 'quoted-printable';

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Encoding::Base64 => 'Base64',
			Encoding::UTF7 => 'UTF-7',
			Encoding::UTF8 => 'UTF-8',
			Encoding::Binary => 'Binary',
			Encoding::QuotedPrintable => 'Quoted Printable',
		};
	}

}