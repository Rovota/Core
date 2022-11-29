<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Views\Components;

class Meta extends Asset
{

	public function __toString(): string
	{
		return $this->formatHtml();
	}

	// -----------------
	
	public function name(string $value): static
	{
		$this->setAttribute('name', $value);
		return $this;
	}

	public function content(string $value): static
	{
		$this->setAttribute('content', $value);
		return $this;
	}

	public function media(string $value): static
	{
		$this->setAttribute('media', $value);
		return $this;
	}

	// -----------------

	public function formatHtml(): string
	{
		$attributes = '';
		foreach ($this->getAttributes() as $name => $value) {
			$attributes .= sprintf(' %s="%s"', $name, $value);
		}

		return sprintf('<meta %s>', trim($attributes)).PHP_EOL;
	}

}