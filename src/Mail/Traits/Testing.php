<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Mail\Traits;

trait Testing
{

	public function hasFrom(): bool
	{
		return empty($this->from) === false;
	}

	public function hasReplyTo(): bool
	{
		return empty($this->reply_to) === false;
	}

	public function hasReceiver(): bool
	{
		return empty($this->receivers) === false;
	}

	// -----------------

	public function hasSubject(): bool
	{
		return mb_strlen($this->subject) > 0;
	}

	public function hasSummary(): bool
	{
		return mb_strlen($this->variables['mail_summary'] ?? '') > 0;
	}

	public function hasView(): bool
	{
		return $this->view !== null;
	}

	public function hasVariable(string $name): bool
	{
		return isset($this->variables[$name]);
	}

	// -----------------

	public function hasUnsubscribe(): bool
	{
		return isset($this->headers['List-Unsubscribe']);
	}

	public function hasHeader(string $name): bool
	{
		return isset($this->headers[$name]);
	}

}