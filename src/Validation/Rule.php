<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation;

use BackedEnum;
use Rovota\Core\Validation\Rules\Advanced\HibpRule;
use Rovota\Core\Validation\Rules\Basic\BetweenRule;
use Rovota\Core\Validation\Rules\Basic\MaxRule;
use Rovota\Core\Validation\Rules\Basic\MinRule;
use Rovota\Core\Validation\Rules\Basic\RangeRule;
use Rovota\Core\Validation\Rules\Basic\SizeRule;
use Rovota\Core\Validation\Rules\DateTime\TimezoneRule;
use Rovota\Core\Validation\Rules\Types\ArrayRule;
use Rovota\Core\Validation\Rules\Types\BooleanRule;
use Rovota\Core\Validation\Rules\Types\EnumRule;
use Rovota\Core\Validation\Rules\Types\FileRule;
use Rovota\Core\Validation\Rules\Types\FloatRule;
use Rovota\Core\Validation\Rules\Types\IntegerRule;
use Rovota\Core\Validation\Rules\Types\MomentRule;
use Rovota\Core\Validation\Rules\Types\NumericRule;
use Rovota\Core\Validation\Rules\Types\StringRule;

final class Rule
{

	protected function __construct()
	{
	}

	// -----------------
	// Advanced

	public static function hibp(int $threshold): HibpRule
	{
		$rule = new HibpRule();
		return $rule->withOptions([$threshold]);
	}

	// -----------------
	// Basic

	public static function between(float|int $min, float|int $max): BetweenRule
	{
		$rule = new BetweenRule();
		return $rule->withOptions([$min, $max]);
	}

	public static function max(float|int $target): MaxRule
	{
		$rule = new MaxRule();
		return $rule->withOptions([$target]);
	}

	public static function min(float|int $target): MinRule
	{
		$rule = new MinRule();
		return $rule->withOptions([$target]);
	}

	public static function range(float|int $min, float|int $max): RangeRule
	{
		$rule = new RangeRule();
		return $rule->withOptions([$min, $max]);
	}

	public static function size(float|int $target): SizeRule
	{
		$rule = new SizeRule();
		return $rule->withOptions([$target]);
	}

	// -----------------
	// DateTime

	public static function timezone(string|array $allowed): TimezoneRule
	{
		$rule = new TimezoneRule();
		return $rule->withOptions([$allowed]);
	}

	// -----------------
	// Storage

	// -----------------
	// Types

	public static function array(): ArrayRule
	{
		return new ArrayRule();
	}

	public static function boolean(): BooleanRule
	{
		return new BooleanRule();
	}

	public static function enum(BackedEnum|string $class): EnumRule
	{
		$rule = new EnumRule();
		return $rule->withOptions([$class]);
	}

	public static function file(): FileRule
	{
		return new FileRule();
	}

	public static function float(): FloatRule
	{
		return new FloatRule();
	}

	public static function integer(): IntegerRule
	{
		return new IntegerRule();
	}

	public static function moment(): MomentRule
	{
		return new MomentRule();
	}

	public static function numeric(): NumericRule
	{
		return new NumericRule();
	}

	public static function string(): StringRule
	{
		return new StringRule();
	}

}