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
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class MimesRule extends Base
{

	protected array $extensions = [];

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if ($value instanceof UploadedFile) {
			$value = $value->variant('original');
		}

		if (!$value instanceof FileInterface) {
			return ValidationAction::NextRule;
		}

		foreach ($this->extensions as $extension) {
			$mime_types = ValidationTools::extensionMimeTypes($extension);
			if (empty($mime_types)) {
				continue;
			}
			if ($value->isAnyMimeType($mime_types)) {
				return ValidationAction::NextRule;
			}
		}

		return new ErrorMessage($this->name, 'The value must be of an allowed type.', data: [
			'allowed' => $this->extensions,
		]);
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (empty($options) === false) {
			$this->extensions = $options;
		}

		return $this;
	}

}