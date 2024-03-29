<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Partials;

use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Partials\Traits\PartialModifiers;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\Traits\Macroable;
use Throwable;

class Partial
{
	use Macroable, PartialModifiers, Conditionable;

	protected string|null $file = null;

	// -----------------

	public function __construct(string|null $file, array $data)
	{
		$this->variables = new Bucket();

		if ($this->file === null) {
			$this->file = $file;
		}

		foreach ($data as $type => $items) {
			if ($type === 'variables') {
				$this->variables->merge($items);
				continue;
			}
			$this->{$type} = $items;
		}
	}

	public function __toString(): string
	{
		$this->render();
		return '';
	}

	// -----------------

	public static function make(array $variables = []): static
	{
		try {
			$partial = PartialManager::make(static::class, null);
			foreach ($variables as $name => $value) {
				$partial->with($name, $value);
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
			exit;
		}
		return $partial;
	}

	// -----------------

	// TODO: Functions to parse scripts and styles

	// -----------------

	public function render(): void
	{
		extract($this->variables->toArray());
		$this->variables->flush();

		include base_path($this->file);
	}

}