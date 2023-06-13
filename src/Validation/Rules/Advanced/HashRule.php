<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Advanced;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Rules\Rule;

class HashRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		[$algorithm, $reference] = $options;
		$hash = hash($algorithm, $reference);

		if ($value !== $hash) {
			return new ErrorMessage($this->name, 'The provided hash is incorrect.', data: [
				'algorithm' => $algorithm,
				'reference' => $reference,
				'hash' => $hash,
			]);
		}
		return true;
	}
}