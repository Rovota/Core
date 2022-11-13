<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Views;

use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\MessageBucket;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\Traits\Errors;
use Rovota\Core\Support\Traits\Macroable;
use Rovota\Core\Views\Traits\ViewModifiers;

class View
{
	use Macroable, ViewModifiers, Errors, Conditionable;

	protected string|null $file = null;

	// -----------------

	public function __construct(string|null $file, array $data, MessageBucket $errors)
	{
		$this->variables = new Bucket();

		if ($this->file === null) {
			$this->file = $file;
		}

		$this->passErrors($errors->toArray());

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
		return $this->render();
	}

	// -----------------

	/**
	 * @throws \Rovota\Core\Views\Exceptions\MissingViewException
	 */
	public static function make(array $variables = []): static
	{
		$view = ViewManager::make(static::class, null);
		foreach ($variables as $name => $value) {
			$view->with($name, $value);
		}
		return $view;
	}

	// -----------------

	// TODO: Functions to parse scripts and styles

	// -----------------

	public function render(): string
	{
		ob_get_clean();
		ob_start();

		extract($this->variables->toArray());
		$errors = $this->errors;
		$this->variables->flush();

		include base_path($this->file);
		return ob_get_clean();
	}

}