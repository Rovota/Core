<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Storage;

use Rovota\Core\Http\UploadedFile;
use Rovota\Core\Storage\Interfaces\FileInterface;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class FileRule extends Base
{

	public function __construct()
	{
		parent::__construct('file');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if ($value instanceof UploadedFile) {
			$value = $value->variant('original');
		}

		if ($value instanceof FileInterface === false) {
			return new ErrorMessage($this->name, 'The value must be a valid file.');
		}

		return ValidationAction::NextRule;
	}

}