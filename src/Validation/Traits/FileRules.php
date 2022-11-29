<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Traits;

use Rovota\Core\Http\UploadedFile;
use Rovota\Core\Storage\File;
use Rovota\Core\Validation\ValidationManager;

trait FileRules
{

	protected function ruleFile(string $field, mixed $data): bool
	{
		if ($data instanceof UploadedFile) {
			$data = $data->variant('original');
		}

		if ($data instanceof File === false) {
			$this->addError($field, 'file');
			return false;
		}
		return true;
	}

	protected function ruleExtensions(string $field, mixed $data, array $allowed): bool
	{
		if ($data instanceof UploadedFile) {
			$data = $data->variant('original');
		}

		if ($data instanceof File) {
			if ($data->isAnyExtension($allowed) === false) {
				$this->addError($field, 'extensions', $allowed);
				return false;
			}
		}

		return true;
	}

	protected function ruleMimeTypes(string $field, mixed $data, array $allowed): bool
	{
		if ($data instanceof UploadedFile) {
			$data = $data->variant('original');
		}

		if ($data instanceof File) {
			if ($data->isAnyMimeType($allowed) === false) {
				$this->addError($field, 'mime_types', $allowed);
				return false;
			}
		}

		return true;
	}

	protected function ruleMimes(string $field, mixed $data, array $allowed): bool
	{
		if ($data instanceof UploadedFile) {
			$data = $data->variant('original');
		}

		if (!$data instanceof File) {
			return true;
		}

		foreach ($allowed as $extension) {
			$mime_types = ValidationManager::extensionMimeTypes($extension);
			if (empty($mime_types)) {
				continue;
			}
			if ($data->isAnyMimeType($mime_types)) {
				return true;
			}
		}

		$this->addError($field, 'mimes', $allowed);
		return false;
	}

}