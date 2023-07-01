<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support\Traits;

use Rovota\Core\Structures\ErrorBucket;
use Rovota\Core\Support\ErrorMessage;

trait Errors
{
	protected ErrorBucket $errors;

	protected array $message_overrides = [];

	// -----------------

	public function errors(): ErrorBucket
	{
		return $this->errors;
	}

	// -----------------

	public function withErrors(ErrorBucket|array $errors): static
	{
		$this->errors->import($errors);
		return $this;
	}

	// -----------------

	protected function setError(string $type, string $identifier, ErrorMessage|string $message): void
	{
		if (isset($this->message_overrides[$type][$identifier])) {
			if (is_string($message)) {
				$message = $this->message_overrides[$type][$identifier];
			} else {
				$message->message = $this->message_overrides[$type][$identifier];
			}
		}

		$this->errors->set($type.'.'.$identifier, $message);
	}

	// -----------------

	public function setMessageOverride(string $type, string $identifier, string $message): static
	{
		$this->message_overrides[$type][$identifier] = trim($message);
		return $this;
	}

	public function setMessageOverrides(array $messages): static
	{
		$this->message_overrides = array_replace_recursive($this->message_overrides, $messages);
		return $this;
	}

	public function findMessageOverride(string $type, string $identifier): string|null
	{
		return $this->message_overrides[$type][$identifier] ?? null;
	}

	// -----------------

	public function clearMessageOverrides(): void
	{
		$this->message_overrides = [];
	}

}