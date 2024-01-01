<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\DateTime;

use DateTime;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class DateFormatRule extends Base
{

	protected array $formats = [];

	// -----------------

	public function __construct()
	{
		parent::__construct('date_format');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		foreach ($this->formats as $format) {
			$date = DateTime::createFromFormat($format, $value);
			if ($date && $date->format($format) === $value) {
				return ValidationAction::NextRule;
			}
		}

		return new ErrorMessage($this->name, 'The value must follow a specified format.', data: [
			'formats' => $this->formats,
		]);
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (empty($options) === false) {
			$this->formats = $options;
		}

		return $this;
	}

}