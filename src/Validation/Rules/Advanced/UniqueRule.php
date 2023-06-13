<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Advanced;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Support\ValidationTools;
use Rovota\Core\Validation\Rules\Rule;

class UniqueRule extends Rule
{

	public function validate(string $attribute, mixed $value, array $options): ErrorMessage|true
	{
		if (!is_string($value) && !is_int($value)) {
			$value = (string)$value;
		}

		$config = ValidationTools::processDatabaseOptions($attribute, $options);
		$occurrences = ValidationTools::getOccurrences($config, $value);

		if ($occurrences > 0) {
			return new ErrorMessage($this->name, 'The provided value must be unique.', data: [
				'value' => $value,
				'occurrences' => $options[0],
			]);
		}
		return true;
	}
}