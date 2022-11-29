<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Rovota\Core\Validation\Validator as ValidatorInstance;

final class Validator
{

	protected function __construct()
	{
	}

	// -----------------

	public static function create(mixed $data = [], array $rules = [], array $messages = []): ValidatorInstance
	{
		return new ValidatorInstance($data, $rules, $messages);
	}

	public static function validate(mixed $data = [], array $rules = [], array $messages = []): bool
	{
		return self::create($data, $rules, $messages)->validate();
	}

}