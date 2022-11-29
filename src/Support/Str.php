<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support;

use Rovota\Core\Convert\ConversionManager;
use Rovota\Core\Localization\LocalizationManager;
use Throwable;
$randomizer = new Random\Randomizer();

final class Str
{

	protected function __construct()
	{
	}

	// -----------------

	public static function make(string $string): FluentString
	{
		return new FluentString($string);
	}

	// -----------------

	public static function translate(string|null $string, array|object $args = [], string|null $source = null): string
	{
		if ($string === null) return '';

		$string = LocalizationManager::getStringTranslation($string, $source);

		if (empty($args) === false) {
			if (is_object($args)) {
				$matches = [];
				if (preg_match_all('#:([a-z\d_]*)#m', $string, $matches) > 0) {
					foreach ($matches[1] as $name) {
						if ($args->{$name} !== null) {
							$string = str_replace(':'.$name, Str::translate($args->{$name}, source: $source), $string);
						}
					}
				}
			} else {
				if (array_is_list($args)) {
					return sprintf($string, ...$args);
				}
				$args = as_bucket($args)->sortBy(fn ($variable, $key) => strlen($key), descending: true);
				foreach ($args as $name => $value) {
					$string = str_replace(':'.$name, Str::translate($value, source: $source), $string);
				}
			}
		}

		return $string;
	}

	public static function escape(string|null $string, string $encoding = 'UTF-8'): string|null
	{
		if ($string === null) {
			return null;
		}
		return htmlentities($string, encoding: $encoding);
	}

	public static function after(string $string, string $target): string
	{
		return str_contains($string, $target) ? explode($target, $string, 2)[1] : $string;
	}

	public static function afterLast(string $string, string $target): string
	{
		if (str_contains($string, $target)) {
			$result = explode($target, $string);
			return end($result);
		}
		return $string;
	}

	public static function append(string $string, string $addition): string
	{
		return $string.$addition;
	}

	public static function basename(string $string, string $suffix = ''): string
	{
		return basename($string, $suffix);
	}

	public static function before(string $string, string $target): string
	{
		return str_contains($string, $target) ? explode($target, $string, 2)[0] : $string;
	}

	public static function beforeLast(string $string, string $target): string
	{
		return str_contains($string, $target) ? substr($string, 0, strrpos($string, $target)) : $string;
	}

	public static function between(string $string, string $start, string $end): string
	{
		if (self::containsAll($string, [$start, $end])) {
			$string = self::after($string, $start);
			return self::beforeLast($string, $end);
		}
		return $string;
	}

	public static function camel(string $string): string
	{
		return lcfirst(self::pascal($string));
	}

	public static function contains(string $string, string $needle): bool
	{
		return str_contains($string, $needle);
	}

	public static function containsAll(string $string, array $values): bool
	{
		foreach ($values as $value) {
			if (!str_contains($string, $value)) {
				return false;
			}
		}
		return true;
	}

	public static function containsAny(string $string, array $values): bool
	{
		foreach ($values as $value) {
			if (str_contains($string, $value)) {
				return true;
			}
		}
		return false;
	}

	public static function containsNone(string $string, array $values): bool
	{
		foreach ($values as $value) {
			if (str_contains($string, $value)) {
				return false;
			}
		}
		return true;
	}

	public static function decrement(string $string, string $separator = '-', int $step = 1): string
	{
		$matches = null;
		preg_match('/(.+)' . preg_quote($separator, '/') . '(\d+)$/', $string, $matches);

		if (isset($matches[2])) {
			$new_value = max((int)$matches[2] - max($step, 0), 0);
			return $new_value === 0 ? $matches[1] : $matches[1].$separator.$new_value;
		}

		return $string;
	}

	public static function dirname(string $string, int $levels = 1): string
	{
		$counter = 0;
		while ($counter < $levels) {
			$string = str_replace(basename($string), '', $string);
			$counter++;
		}
		return rtrim($string, '/');
	}

	public static function endsWith(string $string, string $needle): bool
	{
		return str_ends_with($string, $needle);
	}

	public static function endsWithAny(string $string, array $needles): bool
	{
		foreach ($needles as $needle) {
			if (str_ends_with($string, $needle)) {
				return true;
			}
		}
		return false;
	}

	public static function endsWithNone(string $string, array $needles): bool
	{
		foreach ($needles as $needle) {
			if (str_ends_with($string, $needle)) {
				return false;
			}
		}
		return true;
	}

	public static function explode(string $string, string $char, int $elements = PHP_INT_MAX): array
	{
		return explode($char, $string, $elements);
	}

	public static function finish(string $string, string $value): string
	{
		return str_ends_with($string, $value) ? $string : $string.$value;
	}

	public static function hash(string $string, string $algo = 'md5'): string
	{
		return hash($algo, $string);
	}

	public static function increment(string $string, string $separator = '-', int $step = 1): string
	{
		$matches = null;
		preg_match('/(.+)' . preg_quote($separator, '/') . '(\d+)$/', $string, $matches);

		if (isset($matches[2])) {
			$new_value = (int)$matches[2] + max($step, 0);
			return $matches[1].$separator.$new_value;
		}

		return $string.$separator.$step;
	}

	public static function insert(string $string, int $interval, string $character): string
	{
		return implode($character, str_split($string, $interval));
	}

	public static function isAscii(string $string): bool
	{
		return $string === ConversionManager::toAscii($string);
	}

	public static function isEmpty(string $string): bool
	{
		return self::length(trim($string)) === 0;
	}

	public static function isHash(string $string, string $algo = 'md5'): bool
	{
		return hash($algo, $string) === $string;
	}

	public static function isNotEmpty(string $string): bool
	{
		return self::length(trim($string)) > 0;
	}

	public static function isSlug(string $string, string|null $replacement = null): bool
	{
		return $string === self::slug($string, $replacement);
	}

	public static function kebab(string $string): string
	{
		return self::snake($string, '-');
	}

	public static function length(string $string): int
	{
		return mb_strwidth($string, 'UTF-8');
	}

	public static function limit(string $string, int $start, int $length, string $marker = ''): string
	{
		if (mb_strwidth($string, 'UTF-8') <= $length) {
			return $string;
		} else {
			return mb_strimwidth($string, $start, $length, $marker, 'UTF-8');
		}
	}

	public static function lower(string $string): string
	{
		return mb_strtolower($string, 'UTF-8');
	}

	public static function mask(string $string, string $replacement, int $index, int|null $length = null): string
	{
		// Inspired by the Laravel Str:mask() method.
		if ($replacement === '') {
			return $string;
		}

		$segment = mb_substr($string, $index, $length, 'UTF-8');
		if ($segment === '') {
			return $string;
		}

		$str_length = mb_strlen($string, 'UTF-8');
		$start_index = $index;

		if ($index < 0) {
			$start_index = $index < -$str_length ? 0 : $str_length + $index;
		}

		$start = mb_substr($string, 0, $start_index, 'UTF-8');
		$segment_length = mb_strlen($segment, 'UTF-8');
		$end = mb_substr($string, $start_index + $segment_length);

		return $start.str_repeat(mb_substr($replacement, 0, 1, 'UTF-8'), $segment_length).$end;
	}

	public static function maskEmail(string $string, string $replacement, int $preserve = 3): string
	{
		$maskable = Str::before($string, '@');
		$rest = str_replace($maskable, '', $string);

		return Str::mask($maskable, $replacement, $preserve, strlen($maskable) - $preserve).$rest;
	}

	public static function merge(string $string, string|array $values): string
	{
		if (is_string($values)) {
			$values = [$values];
		}
		return empty($values) === false ? sprintf($string, ...$values) : $string;
	}

	public static function occurrences(string $string, mixed $needle): int
	{
		return max(count(explode((string)$needle, $string)) - 1, 0);
	}

	public static function padBoth(string $string, int $length, string $pad_with = ' '): string
	{
		// Inspired by the Laravel Str::padBoth() method.
		$space = max(0, $length - mb_strlen($string));
		$padding_left = mb_substr(str_repeat($pad_with, floor($space / 2)), 0, floor($space / 2));
		$padding_right = mb_substr(str_repeat($pad_with, ceil($space / 2)), 0, ceil($space / 2));
		return $padding_left.$string.$padding_right;
	}

	public static function padLeft(string $string, int $length, string $pad_with = ' '): string
	{
		// Inspired by the Laravel Str::padLeft() method.
		$space = max(0, $length - mb_strlen($string));
		return mb_substr(str_repeat($pad_with, $space), 0, $space).$string;
	}

	public static function padRight(string $string, int $length, string $pad_with = ' '): string
	{
		// Inspired by the Laravel Str::padRight() method.
		$space = max(0, $length - mb_strlen($string));
		return $string.mb_substr(str_repeat($pad_with, $space), 0, $space);
	}

	public static function pascal(string $string): string
	{
		return str_replace([' ', '_', '-'], '', ucwords($string, ' _-'));
	}

	public static function plural(string $string, mixed $count = 2): string
	{
		return Pluralizer::plural($string, $count);
	}

	public static function prepend(string $string, string $addition): string
	{
		return $addition.$string;
	}

	public static function reverse(string $string): string
	{
		return implode(array_reverse(mb_str_split($string)));
	}

	public static function remove(string $string, string|array $values, bool $ignore_case = false): string
	{
		foreach (is_string($values) ? [$values] : $values as $value) {
			$string = ($ignore_case) ? str_ireplace($value, '', $string) : str_replace($value, '', $string);
		}
		return $string;
	}

	public static function replace(string $string, string|array $targets, string|array $values): string
	{
		return str_replace($targets, $values, $string);
	}

	public static function replaceSequential(string $string, string $target, array $values): string
	{
		$string = str_replace($target, '%s', $string);
		return sprintf($string, ...$values);
	}

	public static function replaceFirst(string $string, string $target, string $value): string
	{
		$position = strpos($string, $target);
		if ($position !== false) {
			return substr_replace($string, $value, $position, strlen($target));
		}
		return $string;
	}

	public static function replaceLast(string $string, string $target, string $value): string
	{
		$position = strrpos($string, $target);
		if ($position !== false) {
			return substr_replace($string, $value, $position, strlen($target));
		}
		return $string;
	}

	public static function scan(string $string, string $format): array
	{
		return sscanf($string, $format);
	}

	public static function scramble(string $string): string
	{
		$words = explode(' ', $string);
		$string = '';

		foreach ($words as $word) {
			if (strlen($word) < 4) {
				$string .= $word.' ';
			} else {
				$string .= sprintf('%s%s%s ', $word[0], $randomizer->shuffleBytes(substr($word, 1, -1)), $word[strlen($word) - 1]);
			}
		}

		return trim($string);
	}

	public static function shuffle(string $string): string
	{
		return $randomizer->shuffleBytes($string);
	}

	public static function simplify(string $string): string
	{
		return ConversionManager::toAscii($string);
	}

	public static function slug(string $string, string $separator = '-'): string
	{
		$string = self::lower(self::simplify($string));
		$string = str_replace(' ', '-', $string);
		$string = preg_replace('/[^a-z\d\-]/', '', $string);
		$string = preg_replace('/-+/', '-', $string);
		$string = rtrim(ltrim($string, '-'), '-');

		return str_replace('-', $separator, $string);
	}

	public static function snake(string $string, string $separator = '_'): string
	{
		preg_match_all('!([A-Z][A-Z\d]*(?=$|[A-Z][a-z\d])|[A-Za-z][a-z\d]+)!', $string, $matches);
		$ret = $matches[0];
		foreach ($ret as &$match) {
			$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
		}
		return implode($separator, $ret);
	}

	public static function start(string $string, string $value): string
	{
		return str_starts_with($string, $value) ? $string : $value.$string;
	}

	public static function startAndFinish(string $string, string $value): string
	{
		$string = str_starts_with($string, $value) ? $string : $value.$string;
		return str_ends_with($string, $value) ? $string : $string.$value;
	}

	public static function startsWith(string $string, string $needle): bool
	{
		return str_starts_with($string, $needle);
	}

	public static function startsWithAny(string $string, array $needles): bool
	{
		foreach ($needles as $needle) {
			if (str_starts_with($string, $needle)) {
				return true;
			}
		}
		return false;
	}

	public static function startsWithNone(string $string, array $needles): bool
	{
		foreach ($needles as $needle) {
			if (str_starts_with($string, $needle)) {
				return false;
			}
		}
		return true;
	}

	public static function swap(string $string, array $map): string
	{
		return strtr($string, $map);
	}

	public static function title(string $string): string
	{
		return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
	}

	public static function trim(string $string, string|null $characters = null): string
	{
		return $characters !== null ? trim($string, $characters) : trim($string);
	}

	public static function trimLeft(string $string, string|null $characters = null): string
	{
		return $characters !== null ? ltrim($string, $characters) : ltrim($string);
	}

	public static function trimRight(string $string, string|null $characters = null): string
	{
		return $characters !== null ? rtrim($string, $characters) : rtrim($string);
	}

	public static function upper(string $string): string
	{
		return mb_strtoupper($string, 'UTF-8');
	}

	public static function wordCount(string $string): int
	{
		return str_word_count($string);
	}

	public static function wrap(string $string, string $value, string|null $end = null): string
	{
		return $value.$string.($end ?? $value);
	}

	// -----------------

	public static function random(int $length): string
	{
		$length = ($length < 4) ? 4 : $length;
		$iteration = 0;
		$bytes = '';

		while ($iteration < 1) {
			try {
				$bytes = random_bytes(($length - ($length % 2)) / 2);
			} catch (Throwable) {
				continue;
			}
			$iteration++;
		}

		return self::limit(self::padRight(bin2hex($bytes), $length, rand(0, 9)), 0, $length);
	}

}