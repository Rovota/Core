<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Views;

use Rovota\Core\Structures\Bucket;
use Rovota\Core\Structures\ErrorBucket;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\Traits\Errors;
use Rovota\Core\Support\Traits\Macroable;
use Rovota\Core\Views\Exceptions\MissingViewException;
use Rovota\Core\Views\Traits\ViewModifiers;

class View
{
	use Macroable, ViewModifiers, Conditionable, Errors;

	protected string|null $file = null;

	// -----------------

	public function __construct(string|null $file, array $data)
	{
		$this->variables = new Bucket();
		$this->errors = new ErrorBucket();

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
		return $this->render();
	}

	// -----------------

	/**
	 * @throws MissingViewException
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
		$this->variables->flush();

		include base_path($this->file);
		return ob_get_clean();
	}

}