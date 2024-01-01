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

class MimeTypesRule extends Base
{

	protected array $mime_types = [];

	// -----------------

	public function __construct()
	{
		parent::__construct('mime_types');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if ($value instanceof UploadedFile) {
			$value = $value->variant('original');
		}

		if ($value instanceof FileInterface && $value->isAnyMimeType($this->mime_types) === false) {
			return new ErrorMessage($this->name, 'The value must be of an allowed type.', data: [
				'allowed' => $this->mime_types,
			]);
		}

		return ValidationAction::NextRule;
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (empty($options) === false) {
			$this->mime_types = $options;
		}

		return $this;
	}

}