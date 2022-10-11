<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Validation\Traits;

use Rovota\Core\Facades\Validator;
use Rovota\Core\Support\Bucket;

trait RequestValidation
{

	protected Bucket|null $safe_data = null;

	// -----------------

	public function safe(): Bucket
	{
		return $this->safe_data instanceof Bucket ? $this->safe_data : new Bucket([]);
	}

	public function validate(array $rules = [], array $messages = []): bool
	{
		$rules = array_merge($this->validationRules(), $rules);
		$messages = array_merge($this->validationMessages(), $messages);

		$validator = Validator::create($this->getInputData(), $rules, $messages);

		if ($validator->fails()) {
			$this->passErrors($validator->getErrors());
			$this->fillSafeData($validator->validated());
			return false;
		}

		$this->fillSafeData($validator->validated());
		return true;
	}

	// -----------------

	protected function validationRules(): array
	{
		return [];
	}

	protected function validationMessages(): array
	{
		return [];
	}

	protected function fillSafeData(array $data): void
	{
		if ($this->safe_data === null) {
			$this->safe_data = new Bucket();
		}
		$this->safe_data->mergeIfMissing($data);
	}

}