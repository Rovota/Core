<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\DateTime;

use DateTimeZone;
use Rovota\Core\Support\Arr;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class TimezoneRule extends Base
{

	protected array $timezones = [];

	// -----------------

	public function __construct()
	{
		parent::__construct('timezone');

		$this->timezones = timezone_identifiers_list();
	}

	// -----------------
	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if (is_string($value) || $value instanceof DateTimeZone) {
			$value = $value instanceof DateTimeZone ? $value->getName() : $value;
			if (Arr::contains($this->timezones, $value)) {
				return ValidationAction::NextRule;
			}
		}

		return new ErrorMessage($this->name, 'The value must be a valid timezone.', data: [
			'timezones' => $this->timezones,
		]);
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (empty($options) === false) {
			$this->timezones = $options;
		}

		return $this;
	}

}