<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation;

use Rovota\Core\Validation\Rules\Advanced\DifferentRule;
use Rovota\Core\Validation\Rules\Advanced\EmailRule;
use Rovota\Core\Validation\Rules\Advanced\EqualRule;
use Rovota\Core\Validation\Rules\Advanced\ExistsRule;
use Rovota\Core\Validation\Rules\Advanced\FilterRule;
use Rovota\Core\Validation\Rules\Advanced\HashRule;
use Rovota\Core\Validation\Rules\Advanced\HibpRule;
use Rovota\Core\Validation\Rules\Advanced\NotRegexRule;
use Rovota\Core\Validation\Rules\Advanced\RegexRule;
use Rovota\Core\Validation\Rules\Advanced\RequiredIfDisabled;
use Rovota\Core\Validation\Rules\Advanced\RequiredIfEnabled;
use Rovota\Core\Validation\Rules\Advanced\UniqueRule;
use Rovota\Core\Validation\Rules\Basic\BetweenRule;
use Rovota\Core\Validation\Rules\Basic\CaseRule;
use Rovota\Core\Validation\Rules\Basic\ContainsAnyRule;
use Rovota\Core\Validation\Rules\Basic\ContainsNoneRule;
use Rovota\Core\Validation\Rules\Basic\ContainsRule;
use Rovota\Core\Validation\Rules\Basic\EndsWithRule;
use Rovota\Core\Validation\Rules\Basic\GreaterThanOrEqualRule;
use Rovota\Core\Validation\Rules\Basic\GreaterThanRule;
use Rovota\Core\Validation\Rules\Basic\InRule;
use Rovota\Core\Validation\Rules\Basic\LessThanOrEqualRule;
use Rovota\Core\Validation\Rules\Basic\LessThanRule;
use Rovota\Core\Validation\Rules\Basic\MaxRule;
use Rovota\Core\Validation\Rules\Basic\MinRule;
use Rovota\Core\Validation\Rules\Basic\NotInRule;
use Rovota\Core\Validation\Rules\Basic\RangeRule;
use Rovota\Core\Validation\Rules\Basic\SizeRule;
use Rovota\Core\Validation\Rules\Basic\StartsWithRule;
use Rovota\Core\Validation\Rules\DateTime\AfterOrEqualRule;
use Rovota\Core\Validation\Rules\DateTime\AfterRule;
use Rovota\Core\Validation\Rules\DateTime\BeforeOrEqualRule;
use Rovota\Core\Validation\Rules\DateTime\BeforeRule;
use Rovota\Core\Validation\Rules\DateTime\BetweenDatesRule;
use Rovota\Core\Validation\Rules\DateTime\DateEqualsRule;
use Rovota\Core\Validation\Rules\DateTime\DateFormatRule;
use Rovota\Core\Validation\Rules\DateTime\OutsideDatesRule;
use Rovota\Core\Validation\Rules\DateTime\TimezoneRule;
use Rovota\Core\Validation\Rules\Storage\ExtensionsRule;
use Rovota\Core\Validation\Rules\Storage\FileRule;
use Rovota\Core\Validation\Rules\Storage\MimesRule;
use Rovota\Core\Validation\Rules\Storage\MimeTypesRule;
use Rovota\Core\Validation\Rules\Types\ArrayRule;
use Rovota\Core\Validation\Rules\Types\BooleanRule;
use Rovota\Core\Validation\Rules\Types\EnumRule;
use Rovota\Core\Validation\Rules\Types\FloatRule;
use Rovota\Core\Validation\Rules\Types\IntegerRule;
use Rovota\Core\Validation\Rules\Types\MomentRule;
use Rovota\Core\Validation\Rules\Types\NumericRule;
use Rovota\Core\Validation\Rules\Types\StringRule;
use Rovota\Core\Validation\Interfaces\RuleInterface;

final class RuleManager
{

	protected static array $rules = [];

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	public static function initialize(): void
	{
		self::registerDefaultRules();
	}

	// -----------------

	public static function register(string $name, string $rule): void
	{
		self::$rules[$name] = $rule;
	}

	// -----------------

	public static function get(string $name): RuleInterface|null
	{
		return isset(self::$rules[$name]) ? new self::$rules[$name]($name) : null;
	}

	// -----------------

	protected static function registerDefaultRules(): void
	{
		// Types
		self::register('array', ArrayRule::class);
		self::register('bool', BooleanRule::class);
		self::register('enum', EnumRule::class);
		self::register('float', FloatRule::class);
		self::register('int', IntegerRule::class);
		self::register('moment', MomentRule::class);
		self::register('numeric', NumericRule::class);
		self::register('string', StringRule::class);

		// Basic
		self::register('size', SizeRule::class);
		self::register('max', MaxRule::class);
		self::register('min', MinRule::class);
		self::register('between', BetweenRule::class);
		self::register('range', RangeRule::class);
		self::register('gt', GreaterThanRule::class);
		self::register('gte', GreaterThanOrEqualRule::class);
		self::register('lt', LessThanRule::class);
		self::register('lte', LessThanOrEqualRule::class);
		self::register('case', CaseRule::class);
		self::register('starts_with', StartsWithRule::class);
		self::register('ends_with', EndsWithRule::class);
		self::register('contains', ContainsRule::class);
		self::register('contains_any', ContainsAnyRule::class);
		self::register('contains_none', ContainsNoneRule::class);
		self::register('in', InRule::class);
		self::register('not_in', NotInRule::class);

		// DateTime
		self::register('after', AfterRule::class);
		self::register('after_or_equal', AfterOrEqualRule::class);
		self::register('before', BeforeRule::class);
		self::register('before_or_equal', BeforeOrEqualRule::class);
		self::register('between_dates', BetweenDatesRule::class);
		self::register('outside_dates', OutsideDatesRule::class);
		self::register('date_equals', DateEqualsRule::class);
		self::register('date_format', DateFormatRule::class);
		self::register('timezone', TimezoneRule::class);

		// File
		self::register('file', FileRule::class);
		self::register('extensions', ExtensionsRule::class);
		self::register('mime_types', MimeTypesRule::class);
		self::register('mimes', MimesRule::class);

		// Advanced
		self::register('regex', RegexRule::class);
		self::register('not_regex', NotRegexRule::class);
		self::register('unique', UniqueRule::class);
		self::register('exists', ExistsRule::class);
		self::register('filter', FilterRule::class);
		self::register('hash', HashRule::class);
		self::register('email', EmailRule::class);
		self::register('different', DifferentRule::class);
		self::register('equal', EqualRule::class);
		self::register('required_if_enabled', RequiredIfEnabled::class);
		self::register('required_if_disabled', RequiredIfDisabled::class);
		self::register('hibp', HibpRule::class);
	}

}