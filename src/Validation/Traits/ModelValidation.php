<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Validation\Traits;

use Rovota\Core\Validation\Validator;

trait ModelValidation
{

	protected Validator|string $validation_class = Validator::class;
	protected array $validation_rules = [];
	protected array $validation_messages = [];

	protected bool $validate_on_assignment = false;
	protected bool $reject_invalid_assignments = false;

	// -----------------

	public function validate(array $rules = [], array $messages = []): bool
	{
		$rules = array_merge($this->validation_rules, $rules);
		$messages = array_merge($this->validation_messages, $messages);

		$validator = $this->validation_class::create($this->attributes_modified, $rules, $messages);

		if ($validator->fails()) {
			$this->passErrors($validator->getErrors());
			return false;
		}

		return true;
	}

	public function validateAssignment(string $attribute, mixed $value): bool
	{
		if ($this->validate_on_assignment === false || !isset($this->validation_rules[$attribute])) {
			return true;
		}

		$validator = $this->validation_class::create([$attribute => $value], $this->getValidationRulesWithType($attribute), $this->getValidationMessagesWithType($attribute));

		if ($validator->fails()) {
			$this->passErrors($validator->getErrors());

			if ($this->reject_invalid_assignments) {
				return false;
			}
		}

		return true;
	}

	// -----------------

	protected function getValidationRulesWithType(string $type): array
	{
		return [$type => $this->validation_rules[$type] ?? []];
	}

	protected function getValidationMessagesWithType(string $type): array
	{
		return [$type => $this->validation_messages[$type] ?? []];
	}

}