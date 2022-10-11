<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Mail\Traits;

use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Mail\MailManager;

trait Addressing
{

	private array $from = [];
	private array $reply_to = [];

	private array $receivers = [];

	// -----------------

	public function from(string $name, string $address): static
	{
		$this->from = ['name' => trim($name), 'address' => trim($address)];
		return $this;
	}

	public function replyTo(string $name, string $address): static
	{
		$this->reply_to = ['name' => trim($name), 'address' => trim($address)];
		return $this;
	}

	// -----------------

	public function to(Identity|string|int|array $name, string|null $address = null): static
	{
		if (is_array($name)) {
			foreach ($name as $key => $value) {
				if ($value instanceof Identity || is_int($name)) {
					$this->to($value);
				} else {
					$this->to($key, $value);
				}
			}
			return $this;
		}

		$receiver = MailManager::getIdentityData($name, $address);
		if ($receiver !== null) {
			$this->receivers[] = $receiver;
		}

		return $this;
	}

}