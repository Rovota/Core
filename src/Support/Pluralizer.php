<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 *
 * Inspired by the Laravel Illuminate/Support/Pluralizer class.
 */

namespace Rovota\Core\Support;

/**
 * @internal
 */
final class Pluralizer
{

	public static array $uncountable = ['data', 'feedback', 'info', 'media', 'meta', 'news', 'series', 'software',];

	public static array $irregular = ['child' => 'children', 'goose' => 'geese', 'man' => 'man', 'woman' => 'woman', 'tooth' => 'tooth', 'foot' => 'feet', 'mouse' => 'mice', 'person' => 'people',];

	// -----------------

	public static function plural(string $value, mixed $count = 2): string
	{
		if (is_countable($count) && !is_int($count)) {
			$count = count($count);
		}

		$value = trim($value);

		if ((int)$count === 1 || self::isUncountable($value)) {
			return $value;
		}

		$plural = match (true) {
			self::isIrregular($value) => self::$irregular[strtolower($value)],
			Text::endsWithAny($value, ['is']) => substr($value, 0, -2).'es',
			Text::endsWithAny($value, ['us']) => substr($value, 0, -2).'i',
			Text::endsWithAny($value, ['s', 'sh', 'ch', 'x', 'z']) => $value.'es',
			Text::endsWithAny($value, ['f', 'fe']) => Text::endsWithAny($value, ['ief', 'of']) ? $value.'s' : substr($value, 0, -1).'ves',
			Text::endsWithAny($value, ['py', 'by', 'ty', 'dy', 'ky', 'gy', 'fy', 'vy', 'sy', 'zy', 'my', 'ny', 'hy', 'ly', 'ry', 'wy', 'jy']) => substr($value, 0, -1).'ies',
			// Text::endsWithAny($value, ['on']) => substr($value, 0, -2).'a',
			default => $value.'s'
		};

		return self::matchCase($plural, $value);
	}

	// -----------------

	protected static function isUncountable(string $value): bool
	{
		return in_array(strtolower($value), self::$uncountable);
	}

	protected static function isIrregular(string $value): bool
	{
		return array_key_exists(strtolower($value), self::$irregular);
	}

	/** @noinspection SpellCheckingInspection */
	protected static function matchCase(string $value, string $comparison): string
	{
		$functions = ['mb_strtolower', 'mb_strtoupper', 'ucfirst', 'ucwords'];

		foreach ($functions as $function) {
			if ($function($comparison) === $comparison) {
				return $function($value);
			}
		}

		return $value;
	}

}