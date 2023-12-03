<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation;

use Closure;
use Rovota\Core\Facades\Session;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Structures\ErrorBucket;
use Rovota\Core\Support\Arr;
use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\Traits\Errors;
use Rovota\Core\Support\Traits\Macroable;
use Rovota\Core\Validation\Interfaces\RuleContextInterface;
use Rovota\Core\Validation\Interfaces\RuleInterface;
use Rovota\Core\Validation\Interfaces\ValidatorInterface;

class Validator implements ValidatorInterface
{
	use Macroable, Errors, Conditionable;

	protected Bucket $unsafe_data;

	protected Bucket $safe_data;

	protected array $rules;

	// -----------------

	public function __construct(mixed $data, array $rules, array $messages = [])
	{
		$this->errors = new ErrorBucket();
		$this->unsafe_data = new Bucket($data);
		$this->safe_data = new Bucket();
		$this->rules = $rules;

		if (empty($messages) === false) {
			$this->setMessageOverrides($messages);
		}
	}

	// -----------------

	public static function create(mixed $data, array $rules, array $messages = []): static
	{
		return new static($data, $rules, $messages);
	}

	// -----------------

	public function succeeds(bool $flash_errors = true): bool
	{
		foreach ($this->rules as $attribute => $rules) {
			$value = $this->unsafe_data->get($attribute);
			if ($this->validateAttribute($attribute, $value, $rules)) {
				$this->safe_data->set($attribute, $value);
			}
		}

		if ($flash_errors === true && $this->errors()->isEmpty() === false) {
			Session::flash('validation_errors', $this->errors());
		}

		return $this->errors()->isEmpty();
	}

	public function fails(): bool
	{
		return $this->succeeds() === false;
	}

	// -----------------

	public function clear(): static
	{
		$this->unsafe_data->flush();
		$this->safe_data->flush();
		$this->rules = [];
		return $this;
	}

	public function addRule(string $attribute, string $name, array|string $options = []): static
	{
		$this->rules[$attribute][$name] = $options;
		return $this;
	}

	public function addData(string $name, mixed $value): static
	{
		$this->unsafe_data->set($name, $value);
		return $this;
	}

	// -----------------

	public function safe(): Bucket
	{
		return $this->safe_data;
	}

	// -----------------

	protected function validateAttribute($attribute, $value, $rules): bool
	{
		$rules = $this->getNormalizedRules($rules);
		$stop_on_failure = array_key_exists('bail', $rules);

		if (array_key_exists('required', $rules) && $this->unsafe_data->missing($attribute)) {
			$this->setError($attribute, 'required', 'This attribute is required.');
			return false;
		}

		if (array_key_exists('sometimes', $rules) && $this->unsafe_data->missing($attribute)) {
			return true;
		}

		if (array_key_exists('nullable', $rules) && $value === null) {
			return true;
		}

		foreach ($rules as $name => $options) {
			if (Arr::contains(['required', 'sometimes', 'nullable', 'bail'], $name)) {
				continue;
			}

			if ($stop_on_failure && $this->errors()->count($attribute) > 0) {
				return false;
			}

			$result = match(true) {
				$options instanceof Closure => $options($value),
				default => $this->getUsableRule($name)->validate($attribute, $value, $options),
			};

			if ($result instanceof ErrorMessage) {
				$this->setError($attribute, $name, $result);
			}
		}

		return $this->errors()->count($attribute) === 0;
	}

	protected function getNormalizedRules(array $rules): array
	{
		$normalized = [];

		foreach ($rules as $name => $options) {
			if (is_int($name)) {
				$normalized[$options] = [];
			} else {
				$normalized[$name] = is_array($options) ? $options : [$options];
			}
		}

		return $normalized;
	}

	protected function getUsableRule(string|RuleInterface $name): RuleInterface|null
	{
		$rule = $name instanceof RuleInterface ? $name : RuleManager::get($name);
		if ($rule instanceof RuleContextInterface) {
			$rule->setContext($this->unsafe_data->toArray());
		}
		return $rule;
	}

}