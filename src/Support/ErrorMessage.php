<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support;

use Rovota\Core\Support\Enums\ErrorLevel;

class ErrorMessage
{

	public string $name;

	public string $message;

	public ErrorLevel $level;

	public array $data = [];

	// -----------------

	public function __construct(string $name, string $message, ErrorLevel|string $level = ErrorLevel::Notice, array $data = [])
	{
		$this->name = $name;
		$this->message = $message;
		$this->level = $level instanceof ErrorLevel ? $level : ErrorLevel::tryFrom($level);

		if (empty($data) === false) {
			$this->data = array_merge_recursive($this->data, $data);
		}
	}

	// -----------------

	public function __toString(): string
	{
		return Str::translate($this->message, $this->data);
	}

}