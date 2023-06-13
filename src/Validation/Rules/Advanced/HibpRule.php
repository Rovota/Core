<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Advanced;

use Rovota\Core\Http\Client\HibpClient;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Rules\Rule;

class HibpRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if (!is_string($value)) {
			$value = (string)$value;
		}

		$hibp = new HibpClient();
		$threshold = $options[0] ?? 0;
		$matches = $hibp->countPasswordMatches(sha1($value));

		if ($matches > $threshold) {
			return new ErrorMessage($this->name, 'The value has appeared in a data breach :count time(s) and should not be used.', data: [
				'count' => $matches,
				'threshold' => $threshold,
			]);
		}
		return true;
	}
}