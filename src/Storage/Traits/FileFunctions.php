<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage\Traits;

use Rovota\Core\Storage\Interfaces\DirectoryInterface;
use Rovota\Core\Storage\Interfaces\FileInterface;
use Rovota\Core\Support\ImageObject;
use Rovota\Core\Support\Str;

trait FileFunctions
{

	public function asImage(): ImageObject|null
	{
		if (extension_loaded('imagick') === false || $this->contents === null) {
			return null;
		}
		return new ImageObject($this->asString(), $this->properties->extension, $this->properties->mime_type);
	}

	public function asHash(string $algo = 'sha256', bool $binary = false): string|null
	{
		if ($this->contents === null) {
			return null;
		}
		return hash($algo, $this->asString(), $binary);
	}

	public function asString(): string
	{
		if (is_resource($this->contents)) {
			return stream_get_contents($this->contents);
		}
		return $this->contents ?? '';
	}

	// -----------------

	public function write(mixed $content): static
	{
		$this->contents = $content;
		$this->unsaved_changes = true;
		return $this;
	}

	// -----------------

	public function compress(string|null $target = null): FileInterface|null
	{
		return $this->properties->disk->compress($this->location(), $target);
	}

	public function extract(string|null $target = null): DirectoryInterface|null
	{
		return $this->properties->disk->extract($this->location(), $target);
	}

	// -----------------

	public function checksum(array $config = []): string
	{
		return $this->properties->disk->checksum($this->location(), $config);
	}

	// -----------------

	public function move(string $to): static
	{
		$this->properties->disk->move($this->location(), $to);
		$this->properties->name = Str::beforeLast(basename($to), '.');
		$this->properties->extension = Str::afterLast(basename($to), '.');
		$this->properties->path = str_replace(basename($to), '', $to);
		return $this;
	}

	public function rename(string $name): static
	{
		$this->properties->disk->rename($this->location(), $name);
		$this->properties->name = Str::beforeLast($name, '.');
		$this->properties->extension = Str::afterLast($name, '.');
		return $this;
	}

	public function copy(string $to): static
	{
		$this->properties->disk->copy($this->location(), $to);
		return $this->properties->disk->file($to);
	}

	// -----------------

	public function delete(): void
	{
		$this->properties->disk->delete($this->location());
	}

	public function clear(): static
	{
		$this->contents = '';
		$this->unsaved_changes = true;
		return $this;
	}

	// -----------------

	public function prepend(string $content, bool $new_line = true): static
	{
		$new_line = empty($this->asString()) === false && $new_line === true;
		$this->contents = Str::finish($content, $new_line ? "\n" : '').$this->asString();
		$this->unsaved_changes = true;
		return $this;
	}

	public function append(string $content, bool $new_line = true): static
	{
		$new_line = empty($this->asString()) === false && $new_line === true;
		$this->contents = Str::finish($this->asString(), $new_line ? "\n" : '').$content;
		$this->unsaved_changes = true;
		return $this;
	}

	public function findAndReplace(array|string $search, array|string $replace, bool $count = false): static|int
	{
		$this->contents = str_replace($search, $replace, $this->asString(), $count);
		$this->unsaved_changes = true;
		return $count ?: $this;
	}

	// -----------------

	public function isExtension(string $extension): bool
	{
		return $this->properties->extension === $extension;
	}

	public function isAnyExtension(array $extensions): bool
	{
		foreach ($extensions as $extension) {
			if ($this->isExtension($extension)) {
				return true;
			}
		}
		return false;
	}

	// -----------------

	public function isMimeType(string $mime_type): bool
	{
		return $this->properties->mime_type === $mime_type;
	}

	public function isAnyMimeType(array $mime_types): bool
	{
		foreach ($mime_types as $mime_type) {
			if ($this->isMimeType($mime_type)) {
				return true;
			}
		}
		return false;
	}

	// -----------------

	public function save(): static
	{
		if ($this->unsaved_changes) {
			if (is_resource($this->contents)) {
				$this->properties->disk->writeStream($this->location(), $this->contents);
				return $this;
			}
			$this->properties->disk->write($this->location(), $this->asString());
		}
		return $this;
	}

	// -----------------

	public function location(): string
	{
		$file_name = sprintf('%s.%s', $this->properties->name, $this->properties->extension);

		return Str::finish($this->properties->path, '/').$file_name;
	}

}