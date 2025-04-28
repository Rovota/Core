<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Storage;

use Rovota\Core\Http\UploadedFile;
use Rovota\Core\Storage\Interfaces\FileInterface;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class ExtensionsRule extends Base
{

	protected array $extensions = [];

	// -----------------

	public function __construct()
	{
		parent::__construct('extensions');
	}

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if ($value instanceof UploadedFile) {
			$value = $value->variant('original');
		}

		if ($value instanceof FileInterface && $value->isAnyExtension($this->extensions) === false) {
			$fail('The file must have one of the allowed extensions.', data: [
				'allowed' => $this->extensions,
			]);
		}

		return ValidationAction::NextRule;
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