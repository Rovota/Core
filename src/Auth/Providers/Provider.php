<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Auth\Providers;

abstract class Provider implements AuthProvider
{

	protected array $trusted_clients = [];

	// -----------------

	public function isClientTrusted(array $attributes = []): bool
	{
		if (empty($this->trusted_clients)) {
			return false;
		}

		foreach ($this->trusted_clients as $trusted_client) {

			$valid_attributes = 0;

			foreach ($attributes as $name => $value) {
				if ($trusted_client->{$name} !== $value) {
					continue;
				}
				$valid_attributes++;
			}

			if ($valid_attributes === count($attributes)) {
				return true;
			}
		}

		return false;
	}

}