<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Partials\Traits;

use Rovota\Core\Partials\Partial;
use Rovota\Core\Support\Bucket;

trait PartialModifiers
{

	protected Bucket|null $variables = null;

	// -----------------

	// TODO: Ability to add styles to the partial snippet. Use partial_styles() or something to echo them.

	// -----------------

	// TODO: Ability to add scripts to the partial snippet. Use partial_scripts() or something to echo them.

	// -----------------

	public function with(array|string $name, mixed $value = null): Partial
	{
		if (is_array($name)) {
			foreach ($name as $key => $value) {
				$this->with($key, $value);
			}
		} else {
			$this->variables->set($name, $value);
		}
		return $this;
	}

	public function getVariables(): Bucket
	{
		return $this->variables ?? new Bucket();
	}

}