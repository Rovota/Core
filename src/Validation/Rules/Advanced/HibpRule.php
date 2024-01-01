<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Advanced;

use Rovota\Core\Http\Client\HibpClient;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class HibpRule extends Base
{

	protected int $threshold = 0;

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if (!is_string($value)) {
			$value = (string)$value;
		}

		$hibp = new HibpClient();
		$matches = $hibp->countPasswordMatches(sha1($value));

		if ($matches > $this->threshold) {
			return new ErrorMessage($this->name, 'The value has appeared in a data breach :count time(s) and should not be used.', data: [
				'count' => $matches,
				'threshold' => $this->threshold,
			]);
		}

		return ValidationAction::NextRule;
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (isset($options[0])) {
			$this->threshold = $options[0];
		}

		return $this;
	}

}