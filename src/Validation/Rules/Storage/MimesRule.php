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
use Rovota\Core\Support\ValidationTools;
use Rovota\Core\Validation\Rules\Rule;

class MimesRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if ($value instanceof UploadedFile) {
			$value = $value->variant('original');
		}

		if (!$value instanceof FileInterface) {
			return true;
		}

		foreach ($options as $extension) {
			$mime_types = ValidationTools::extensionMimeTypes($extension);
			if (empty($mime_types)) {
				continue;
			}
			if ($value->isAnyMimeType($mime_types)) {
				return true;
			}
		}

		return new ErrorMessage($this->name, 'The value must be of an allowed type.', data: [
			'allowed' => $options,
		]);
	}
}