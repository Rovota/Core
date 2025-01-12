<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Mail\Traits;

use Rovota\Core\Storage\Interfaces\FileInterface;
use Rovota\Core\Storage\StorageManager;
use Rovota\Core\Support\ValidationTools;

trait Contents
{
	private array $attachments = [];

	// -----------------

	/**
	 * @throws FilesystemException
	 */
	public function attachment(FileInterface|string $file, string|null $name = null, string|null $mime_type = null, Encoding $encoding = Encoding::UTF8): static
	{
		if (is_string($file)) {
			$file = StorageManager::get()->file($file);
		}

		if ($file instanceof FileInterface) {
			$name = $name ?? (sprintf('%s.%s', $file->properties()->name, $file->properties()->extension));
			$this->stringAttachment($file->contents, $name, $mime_type ?? $file->properties()->mime_type, $encoding);
		}

		return $this;
	}

	public function stringAttachment(string $content, string $name, string $mime_type, Encoding $encoding = Encoding::UTF8): static
	{
		if (Str::contains($name, '.') === false) {
			$extensions = ValidationTools::mimeTypeExtensions($mime_type);
			$name = Str::finish($name.'.', empty($extensions) ? 'txt' : $extensions[0]);
		}

		$this->attachments[] = [
			'content' => trim($content),
			'name' => trim($name),
			'encoding' => $encoding->value,
			'mime_type' => $mime_type,
		];

		return $this;
	}

}