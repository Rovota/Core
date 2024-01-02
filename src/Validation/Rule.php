<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation;

use BackedEnum;
use Rovota\Core\Validation\Rules\Advanced\HibpRule;
use Rovota\Core\Validation\Rules\Types\EnumRule;

final class Rule
{

	protected function __construct()
	{
	}

	// -----------------
	// Types

	public static function enum(BackedEnum|string $class): EnumRule
	{
		$rule = new EnumRule();
		return $rule->withOptions([$class]);
	}

	// -----------------
	// Basic

	// -----------------
	// DateTime

	// -----------------
	// File

	// -----------------
	// Advanced

	public static function hibp(int $threshold): HibpRule
	{
		$rule = new HibpRule();
		return $rule->withOptions([$threshold]);
	}

}