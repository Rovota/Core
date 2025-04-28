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

class ExistsRule extends Base
{

	protected array $config = [];

	// -----------------

	public function __construct()
	{
		parent::__construct('exists');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		if (!is_string($value) && !is_int($value)) {
			$value = (string)$value;
		}

		$config = ValidationTools::processDatabaseOptions($attribute, $this->config);
		$occurrences = ValidationTools::getOccurrences($config, $value);

		if ($occurrences === 0) {
			return new ErrorMessage($this->name, 'There are no matching results for :value.', data: [
				'value' => $value,
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