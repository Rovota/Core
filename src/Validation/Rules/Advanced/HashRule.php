<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Rules\Advanced;

use Rovota\Core\Support\ErrorMessage;
use Rovota\Core\Validation\Enums\ValidationAction;
use Rovota\Core\Validation\Rules\Base;

class HashRule extends Base
{

	protected string $algorithm = 'sha1';
	protected string $reference = '-';

	// -----------------

	public function __construct()
	{
		parent::__construct('hash');
	}

	// -----------------

	public function validate(string $attribute, mixed $value): ErrorMessage|ValidationAction
	{
		$hash = hash($this->algorithm, $this->reference);

		if ($value !== $hash) {
			return new ErrorMessage($this->name, 'The provided hash is incorrect.', data: [
				'algorithm' => $this->algorithm,
				'reference' => $this->reference,
				'hash' => $hash,
			]);
		}

		return ValidationAction::NextRule;
	}

	// -----------------

	public function withOptions(array $options): static
	{
		if (empty($options) === false) {
			$this->algorithm = $options[0];
			$this->reference = $options[1];
		}

		return $this;
	}

}