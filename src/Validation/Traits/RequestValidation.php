<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Traits;

use Rovota\Core\Structures\Bucket;
use Rovota\Core\Validation\Validator;

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
		$validator = Validator::create($this->getInputData(), $rules, $messages);

		if ($validator->succeeds() === false) {
			$this->errors()->import($validator->errors());
			$this->fillSafeData($validator->safe()->toArray());
			return false;
		}

		$this->fillSafeData($validator->safe()->toArray());
		return true;
	}

	// -----------------

	protected function fillSafeData(array $data): void
	{
		if ($this->safe_data === null) {
			$this->safe_data = new Bucket();
		}
		$this->safe_data->merge($data, true);
	}

}