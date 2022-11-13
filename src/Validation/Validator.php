<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation;

use Closure;
use Rovota\Core\Database\DatabaseManager;
use Rovota\Core\Http\UploadedFile;
use Rovota\Core\Storage\File;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Arr;
use Rovota\Core\Support\Text;
use Rovota\Core\Support\Traits\Errors;
use Rovota\Core\Support\Traits\Macroable;
use Rovota\Core\Validation\Traits\AdvancedRules;
use Rovota\Core\Validation\Traits\BasicRules;
use Rovota\Core\Validation\Traits\DateTimeRules;
use Rovota\Core\Validation\Traits\FileRules;

class Validator
{
	use Macroable, Errors, BasicRules, DateTimeRules, AdvancedRules, FileRules;

	protected Bucket $data;
	protected Bucket $data_validated;

	protected array $rules;

	// -----------------

	public function __construct(mixed $data = [], array $rules = [], array $messages = [])
	{
		$this->data = new Bucket($data);
		$this->data_validated = new Bucket();
		$this->rules = $rules;

		if (empty($messages) === false) {
			$this->addErrorMessages($messages);
		}
	}

	// -----------------

	public static function create(mixed $data = [], array $rules = [], array $messages = []): static
	{
		return new static($data, $rules, $messages);
	}

	// -----------------

	public function clear(): void
	{
		$this->data->flush();
		$this->data_validated->flush();
		$this->rules = [];
		$this->clearErrorMessages();
		$this->clearErrors();
	}

	// -----------------

	public function validate(): bool
	{
		foreach ($this->rules as $field => $rules) {
			$data = $this->data->get($field);
			if ($this->validateField($field, $data, $rules)) {
				$this->data_validated->set($field, $data);
			}
		}

		return $this->hasErrors() === false;
	}

	public function fails(): bool
	{
		return $this->validate() === false;
	}

	public function populate(mixed $data = [], array $rules = [], array $messages = []): static
	{
		$this->data = new Bucket($data);
		$this->rules = $rules;

		if (empty($messages) === false) {
			$this->addErrorMessages($messages);
		}

		return $this;
	}

	public function safe(): Bucket
	{
		return $this->data_validated;
	}

	// -----------------

	protected function validateField(string $field, mixed $data, array $rules): bool
	{
		$rules = $this->getNormalizedRules($rules);
		$stop_on_failure = array_key_exists('bail', $rules);

		if (array_key_exists('required', $rules) && !$this->data->has($field)) {
			$this->addError($field, 'required');
			return false;
		}

		if (array_key_exists('sometimes', $rules) && !$this->data->has($field)) {
			return true;
		}

		if (Arr::hasNone($rules, ['nullable', 'required_if_enabled', 'required_if_disabled']) && $data === null) {
			$this->addError($field, 'nullable');
			return false;
		}

		foreach ($rules as $name => $options) {
			if (Arr::contains(['required', 'sometimes', 'nullable', 'bail'], $name)) {
				continue;
			}

			if ($stop_on_failure && $this->hasErrors($field)) {
				return false;
			}

			if ($options instanceof Closure) {
				if ($options($data) === false) {
					$this->addError($field, $name);
				}
				continue;
			}

			$method = 'rule'.Text::pascal($name);
			if (method_exists($this, $method)) {
				$this->{$method}($field, $data, $options);
			}

		}

		return $this->hasErrors($field) === false;
	}

	protected function getNormalizedRules(array $rules): array
	{
		$normalized = [];
		foreach ($rules as $name => $options) {
			if (is_int($name)) {
				$normalized[$options] = null;
			} else {
				$normalized[$name] = $options;
			}
		}

		return $normalized;
	}

	protected function getSize(mixed $data): int|float
	{
		return match(true) {
			$data instanceof File => round($data->size / 1024), // Bytes to Kilobytes
			$data instanceof UploadedFile => round($data->variant('original')->size / 1024), // Bytes to Kilobytes
			is_int($data), is_float($data) => $data,
			is_numeric($data), is_string($data) => Text::length($data),
			is_array($data) => count($data),
			default => 0
		};
	}

	// -----------------

	protected function processDatabaseOptions(string $field, string|array $options): array
	{
		$config = [
			'connection' => DatabaseManager::getDefault(),
			'column' => $field,
			'model' => null,
		];

		if (is_string($options)) {
			$options = [$options];
		}

		if (str_contains($options[0],'\\')) {
			$model = new $options[0]();
			$config['connection'] = $model->getConnection();
			$config['table'] = $model->getTable();
			$config['model'] = $model::class;
		} else if (str_contains($options[0],'.')) {
			$location = explode('.', $options[0]);
			$config['connection'] = $location[0];
			$config['table'] = $location[1];
		} else {
			$config['table'] = $options[0];
		}

		if (isset($options[1]) && is_string($options[1])) {
			$config['column'] = $options[1];
		}

		return $config;
	}

}