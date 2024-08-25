<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http\Traits;

use Rovota\Core\Http\UploadedFile;

trait RequestInput
{
	public readonly RequestData $post;
	public readonly RequestData $query;

	// -----------------

	public function hasFile(string $key): bool
	{
		return $this->post->has($key) && $this->post->get($key) instanceof UploadedFile;
	}

	public function file(string $key): UploadedFile|null
	{
		$file = $this->post->get($key);
		return $file instanceof UploadedFile ? $file : null;
	}

	public function files(string $key): array
	{
		$files = $this->post->get($key, []);
		$result = [];

		foreach (is_array($files) ? $files : [] as $key => $file) {
			if ($file instanceof UploadedFile) {
				$result[$key] = $file;
			}
		}

		return $result;
	}

}