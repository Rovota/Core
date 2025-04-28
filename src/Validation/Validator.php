<?php

/**
 * @copyright   Léandro Tijink
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
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Interfaces\ContextAware;
use Rovota\Core\Validation\Interfaces\RuleInterface;
use Rovota\Core\Validation\Interfaces\ValidatorInterface;

class Validator implements ValidatorInterface
{
	use Macroable, Errors, Conditionable;

	protected Bucket $unsafe_data;

	protected Bucket $safe_data;

	protected array $rules;

	// -----------------

	public function __construct(mixed $data, array $rules = [], array $messages = [])
	{
		$this->errors = new ErrorBucket();
		$this->unsafe_data = new Bucket($data);
		$this->safe_data = new Bucket();

		$this->rules = array_replace_recursive($this->rules(), $rules);

		if (empty($messages) === false || empty($this->messages()) === false) {
			$this->setMessageOverrides(array_replace_recursive($this->messages(), $messages));
		}
	}

	// -----------------

	public static function create(mixed $data, array $rules, array $messages = []): static
	{
		return new static($data, $rules, $messages);
	}

	// -----------------

	protected function rules(): array
	{
		return [];
	}

	protected function messages(): array
	{
		return [];
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

	public function withRule(string $attribute, string $name, array|string $options = []): static
	{
		$this->rules[$attribute][$name] = $options;
		return $this;
	}

	public function withData(string $name, mixed $value): static
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

	protected function validateAttribute(string $attribute, mixed $value, array|object|string $rules): bool
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

			$result = null;

			if ($options instanceof Closure) {
				$result = $options($value);
			} else {
				$rule = match(true) {
					$options instanceof RuleInterface => $this->getUsableRule($options, []),
					default => $this->getUsableRule($name, $options),
				};

				if ($rule instanceof RuleInterface) {
					$result = $rule->validate($attribute, $value);
				}
			}

			if ($result === ValidationAction::NextField) {
				return $this->errors()->count($attribute) === 0;
			}

			if ($result instanceof ErrorMessage) {
				$this->setError($attribute, $result->name, $result);
			}
		}

		return $this->errors()->count($attribute) === 0;
	}

	protected function getNormalizedRules(array|object|string $rules): array
	{
		$rules = is_array($rules) ? $rules : [$rules];
		$normalized = [];

		foreach ($rules as $name => $options) {
			if ($options instanceof RuleInterface) {
				$normalized[$options->getName()] = $options;
				continue;
			}

			if ($options instanceof Closure) {
				$normalized[$name] = $options;
				continue;
			}

			if (is_int($name)) {
				$normalized[$options] = [];
			} else {
				$normalized[$name] = is_array($options) ? $options : [$options];
			}
		}

		return $normalized;
	}

	protected function getUsableRule(string|RuleInterface $name, array $options): RuleInterface|null
	{
		$rule = $name instanceof RuleInterface ? $name : RuleManager::get($name);

		if ($rule instanceof RuleInterface && empty($options) === false) {
			$rule->withOptions($options);
		}

		if ($rule instanceof ContextAware) {
			$rule->withContext($this->unsafe_data->toArray());
		}

		return $rule;
	}

}