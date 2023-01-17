<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage\Traits;

use Rovota\Core\Storage\Interfaces\FileInterface;
use Rovota\Core\Structures\Sequence;
use Rovota\Core\Support\Str;

trait DirectoryFunctions
{

	public function contents(): Sequence
	{
		return $this->properties->disk->contents($this->location());
	}

	public function files(): Sequence
	{
		return $this->properties->disk->files($this->location());
	}

	public function directories(): Sequence
	{
		return $this->properties->disk->directories($this->location());
	}

	// -----------------

	public function exists(string $location): bool
	{
		return $this->properties->disk->exists($this->location().'/'.$location);
	}

	public function missing(string $location): bool
	{
		return $this->properties->disk->missing($this->location().'/'.$location);
	}

	// -----------------

	public function checksum(string $location, array $config = []): string
	{
		return $this->properties->disk->checksum($this->location().'/'.$location, $config);
	}

	// -----------------

	public function compress(string|null $target = null): FileInterface|null
	{
		return $this->properties->disk->compress($this->location(), $target);
	}

	// -----------------

	public function move(string $to): static
	{
		$this->properties->disk->move($this->location(), $to);
		$this->properties->name = basename($to);
		$this->properties->path = str_replace(basename($to), '', $to);
		return $this;
	}

	public function rename(string $name): static
	{
		$this->properties->disk->rename($this->location(), $name);
		$this->properties->name = $name;
		return $this;
	}

	public function copy(string $to): static
	{
		$this->properties->disk->copy($this->location(), $to);
		return $this->properties->disk->directory($to);
	}

	// -----------------

	public function delete(): void
	{
		$this->properties->disk->deleteDirectory($this->location());
	}

	public function clear(): void
	{
		$this->properties->disk->clearDirectory($this->location());
	}

	// -----------------

	protected function location(): string
	{
		return Str::finish($this->properties->path, '/').$this->properties->name;
	}

}