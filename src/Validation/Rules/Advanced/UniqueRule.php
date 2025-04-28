<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Advanced;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Support\ValidationTools;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class UniqueRule extends Base
{

	protected array $config = [];

	// -----------------

	public function __construct()
	{
		parent::__construct('unique');
	}

	// -----------------

	public function validate(mixed $value, Closure $fail): void
	{
		if (!is_string($value) && !is_int($value)) {
			$value = (string)$value;
		}

		$config = ValidationTools::processDatabaseOptions($attribute, $this->config);
		$occurrences = ValidationTools::getOccurrences($config, $value);

		if ($occurrences > 0) {
			$fail('The provided value must be unique.', data: [
				'value' => $value,
				'occurrences' => $occurrences,
			]);
		}

		return ValidationAction::NextRule;
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (empty($options) === false) {
			$this->config = $options;
		}

		return $this;
	}

}