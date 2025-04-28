<?php

/**
 * @copyright   Léandro Tijink
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

	public function validate(mixed $value, Closure $fail): void
	{
		$hash = hash($this->algorithm, $this->reference);

		if ($value !== $hash) {
			$fail('The provided hash is incorrect.', data: [
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