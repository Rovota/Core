<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support;

use Closure;
use DateTimeZone;
use JsonSerializable;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\Traits\Macroable;
use Stringable;

final class Text implements Stringable, JsonSerializable
{
	use Macroable, Conditionable;

	protected string $string;

	// -----------------

	public function __construct(Stringable|string $string)
	{
		$this->string = $string;
	}

	public function __toString(): string
	{
		return $this->string;
	}

	public function jsonSerialize(): string
	{
		return $this->string;
	}

	// -----------------

	public function toString(): string
	{
		return $this->string;
	}

	public function toInteger(): string
	{
		return intval($this->string);
	}

	public function toFloat(): string
	{
		return floatval($this->string);
	}

	public function toBool(): bool
	{
		return filter_var($this->string, FILTER_VALIDATE_BOOLEAN);
	}

	public function toMoment(string $format = null, DateTimeZone|string|null $timezone = null): Moment
	{
		if ($format === null) {
			return Moment::create($this->string, $timezone);
		}
		return Moment::createFromFormat($format, $this->string, $timezone);
	}

	// -----------------

	public function translate(array|object $args = [], string|null $source = null): Text
	{
		$this->string = Str::translate($this->string, $args, $source);
		return $this;
	}

	public function escape(string $encoding = 'UTF-8'): Text
	{
		$this->string = Str::escape($this->string, $encoding);
		return $this;
	}

	// -----------------

	public function acronym(string $delimiter = '.'): Text
	{
		$this->string = Str::acronym($this->string, $delimiter);
		return $this;
	}

	public function after(Stringable|string $target): Text
	{
		$this->string = Str::after($this->string, $target);
		return $this;
	}

	public function afterLast(Stringable|string $target): Text
	{
		$this->string = Str::afterLast($this->string, $target);
		return $this;
	}

	public function append(Stringable|string $addition): Text
	{
		$this->string = Str::append($this->string, $addition);
		return $this;
	}

	public function basename(string $suffix = ''): Text
	{
		$this->string = Str::basename($this->string, $suffix);
		return $this;
	}

	public function before(Stringable|string $target): Text
	{
		$this->string = Str::before($this->string, $target);
		return $this;
	}

	public function beforeLast(Stringable|string $target): Text
	{
		$this->string = Str::beforeLast($this->string, $target);
		return $this;
	}

	public function between(Stringable|string $start, Stringable|string $end): Text
	{
		$this->string = Str::between($this->string, $start, $end);
		return $this;
	}

	public function camel(): Text
	{
		$this->string = Str::camel($this->string);
		return $this;
	}

	public function contains(Stringable|string|array $needle): bool
	{
		return Str::contains($this->string, $needle);
	}

	public function containsAny(array $needles): bool
	{
		return Str::containsAny($this->string, $needles);
	}

	public function containsNone(array $needles): bool
	{
		return Str::containsNone($this->string, $needles);
	}

	public function decrement(string $separator = '-', int $step = 1): string
	{
		$this->string = Str::decrement($this->string, $separator, $step);
		return $this;
	}

	public function dirname(int $levels = 1): Text
	{
		$this->string = Str::dirname($this->string, $levels);
		return $this;
	}

	public function endsWith(Stringable|string $needle): bool
	{
		return Str::endsWith($this->string, $needle);
	}

	public function endsWithAny(array $needles): bool
	{
		return Str::endsWithAny($this->string, $needles);
	}

	public function endsWithNone(array $needles): bool
	{
		return Str::endsWithNone($this->string, $needles);
	}

	public function explode(string $char, int $elements = PHP_INT_MAX): array
	{
		return Str::explode($this->string, $char, $elements);
	}

	public function finish(Stringable|string $value): Text
	{
		$this->string = Str::finish($this->string, $value);
		return $this;
	}

	public function hash(string $algo = 'md5'): Text
	{
		$this->string = Str::hash($this->string, $algo);
		return $this;
	}

	public function increment(string $separator = '-', int $step = 1): Text
	{
		$this->string = Str::increment($this->string, $separator, $step);
		return $this;
	}

	public function insert(int $interval, string $character): Text
	{
		$this->string = Str::insert($this->string, $interval, $character);
		return $this;
	}

	public function isAscii(): bool
	{
		return Str::isAscii($this->string);
	}

	public function isEmpty(): bool
	{
		return Str::isEmpty($this->string);
	}

	public function isHash(string $algo = 'md5'): bool
	{
		return Str::isHash($this->string, $algo);
	}

	public function isNotEmpty(): bool
	{
		return Str::isNotEmpty($this->string);
	}

	public function isSlug(): bool
	{
		return Str::isSlug($this->string);
	}

	public function kebab(): Text
	{
		$this->string = Str::kebab($this->string);
		return $this;
	}

	public function length(): int
	{
		return Str::length($this->string);
	}

	public function limit(int $length, string $marker = ''): Text
	{
		$this->string = Str::limit($this->string, 0, $length, $marker);
		return $this;
	}

	public function lower(): Text
	{
		$this->string = Str::lower($this->string);
		return $this;
	}

	public function mask(string $replacement, int $index, int|null $length = null): Text
	{
		$this->string = Str::mask($this->string, $replacement, $index, $length);
		return $this;
	}

	public function maskEmail(string $replacement, int $preserve = 3): Text
	{
		$this->string = Str::maskEmail($this->string, $replacement, $preserve);
		return $this;
	}

	public function match(string $pattern): Text
	{
		$pattern = Str::startAndFinish($pattern, '/');
		preg_match($pattern, $this->string, $matches);
		return new Text($matches[1] ?? '');
	}

	public function matchAll(string $pattern): Bucket
	{
		$pattern = Str::startAndFinish($pattern, '/');
		preg_match($pattern, $this->string, $matches);
		array_shift($matches);
		return new Bucket($matches);
	}

	public function matches(string $pattern): bool
	{
		$pattern = Str::startAndFinish($pattern, '/');
		return preg_match($pattern, $this->string) === 1;
	}

	public function merge(Stringable|string|array $values): Text
	{
		$this->string = Str::merge($this->string, $values);
		return $this;
	}

	public function modify(Closure $callback): Text
	{
		$callback($this);
		return $this;
	}

	public function occurrences(mixed $needle): int
	{
		return Str::occurrences($this->string, $needle);
	}

	public function padBoth(int $length, string $pad_with = ' '): Text
	{
		$this->string = Str::padBoth($this->string, $length, $pad_with);
		return $this;
	}

	public function padLeft(int $length, string $pad_with = ' '): Text
	{
		$this->string = Str::padLeft($this->string, $length, $pad_with);
		return $this;
	}

	public function padRight(int $length, string $pad_with = ' '): Text
	{
		$this->string = Str::padRight($this->string, $length, $pad_with);
		return $this;
	}

	public function pascal(): Text
	{
		$this->string = Str::pascal($this->string);
		return $this;
	}

	public function plural(mixed $count = 2): Text
	{
		$this->string = Str::plural($this->string, $count);
		return $this;
	}

	public function prepend(Stringable|string $addition): Text
	{
		$this->string = Str::prepend($this->string, $addition);
		return $this;
	}

	public function reverse(): Text
	{
		$this->string = Str::reverse($this->string);
		return $this;
	}

	public function remove(Stringable|string|array $values, bool $ignore_case = false): Text
	{
		$this->string = Str::remove($this->string, $values, $ignore_case);
		return $this;
	}

	public function replace(Stringable|string|array $targets, Stringable|string|array $values): Text
	{
		$this->string = Str::replace($this->string, $targets, $values);
		return $this;
	}

	public function replaceSequential(Stringable|string $target, Stringable|string|array $values): Text
	{
		$this->string = Str::replaceSequential($this->string, $target, $values);
		return $this;
	}

	public function replaceFirst(Stringable|string $target, Stringable|string $value): Text
	{
		$this->string = Str::replaceFirst($this->string, $target, $value);
		return $this;
	}

	public function replaceLast(Stringable|string $target, Stringable|string $value): Text
	{
		$this->string = Str::replaceLast($this->string, $target, $value);
		return $this;
	}

	public function scan(string $format): Bucket
	{
		return new Bucket(Str::scan($this->string, $format));
	}

	public function scramble(): Text
	{
		$this->string = Str::scramble($this->string);
		return $this;
	}

	public function shuffle(): Text
	{
		$this->string = Str::shuffle($this->string);
		return $this;
	}

	public function simplify(): Text
	{
		$this->string = Str::simplify($this->string);
		return $this;
	}

	public function slug(string $separator = '-'): Text
	{
		$this->string = Str::slug($this->string, $separator);
		return $this;
	}

	public function snake(string $separator = '_'): Text
	{
		$this->string = Str::snake($this->string, $separator);
		return $this;
	}

	public function start(Stringable|string $value): Text
	{
		$this->string = Str::start($this->string, $value);
		return $this;
	}

	public function startAndFinish(Stringable|string $value): Text
	{
		$this->string = Str::startAndFinish($this->string, $value);
		return $this;
	}

	public function startsWith(Stringable|string $needle): bool
	{
		return Str::startsWith($this->string, $needle);
	}

	public function startsWithAny(array $needles): bool
	{
		return Str::startsWithAny($this->string, $needles);
	}

	public function startsWithNone(array $needles): bool
	{
		return Str::startsWithNone($this->string, $needles);
	}

	public function swap(array $map): Text
	{
		$this->string = Str::swap($this->string, $map);
		return $this;
	}

	public function tap(callable $callback): Text
	{
		$callback($this);
		return $this;
	}

	public function title(): Text
	{
		$this->string = Str::title($this->string);
		return $this;
	}

	public function trim(string|null $characters = null): Text
	{
		$this->string = Str::trim($this->string, $characters);
		return $this;
	}

	public function trimEnd(string|null $characters = null): Text
	{
		$this->string = Str::trimEnd($this->string, $characters);
		return $this;
	}


	public function trimStart(string|null $characters = null): Text
	{
		$this->string = Str::trimStart($this->string, $characters);
		return $this;
	}
	public function upper(): Text
	{
		$this->string = Str::upper($this->string);
		return $this;
	}

	public function whenEmpty(callable $callback, callable|null $alternative = null): Text
	{
		return $this->when($this->isEmpty(), $callback, $alternative);
	}

	public function whenNotEmpty(callable $callback, callable|null $alternative = null): Text
	{
		return $this->when($this->isNotEmpty(), $callback, $alternative);
	}

	public function whenMatches(string $pattern, callable $callback, callable|null $alternative = null): Text
	{
		$pattern = Str::startAndFinish($pattern, '/');
		return $this->when(preg_match($pattern, $this->string) === 1, $callback, $alternative);
	}

	public function wordCount(): int
	{
		return Str::wordCount($this->string);
	}

	public function wrap(string $value, string|null $end = null): Text
	{
		$this->string = Str::wrap($this->string, $value, $end);
		return $this;
	}

}