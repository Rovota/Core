<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Support;

use Closure;
use DateTimeZone;
use JsonSerializable;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\Traits\Macroable;
use Stringable;

final class FluentString implements Stringable, JsonSerializable
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
		if (is_null($format)) {
			return Moment::create($this->string, $timezone);
		}
		return Moment::createFromFormat($format, $this->string, $timezone);
	}

	// -----------------

	public function translate(array|object $args = [], string|null $source = null): FluentString
	{
		$this->string = Text::translate($this->string, $args, $source);
		return $this;
	}

	public function escape(string $encoding = 'UTF-8'): FluentString
	{
		$this->string = Text::escape($this->string, $encoding);
		return $this;
	}

	// -----------------

	public function after(Stringable|string $target): FluentString
	{
		$this->string = Text::after($this->string, $target);
		return $this;
	}

	public function afterLast(Stringable|string $target): FluentString
	{
		$this->string = Text::afterLast($this->string, $target);
		return $this;
	}

	public function append(Stringable|string $addition): FluentString
	{
		$this->string = Text::append($this->string, $addition);
		return $this;
	}

	public function basename(string $suffix = ''): FluentString
	{
		$this->string = Text::basename($this->string, $suffix);
		return $this;
	}

	public function before(Stringable|string $target): FluentString
	{
		$this->string = Text::before($this->string, $target);
		return $this;
	}

	public function beforeLast(Stringable|string $target): FluentString
	{
		$this->string = Text::beforeLast($this->string, $target);
		return $this;
	}

	public function between(Stringable|string $start, Stringable|string $end): FluentString
	{
		$this->string = Text::between($this->string, $start, $end);
		return $this;
	}

	public function camel(): FluentString
	{
		$this->string = Text::camel($this->string);
		return $this;
	}

	public function contains(Stringable|string $needle): bool
	{
		return Text::contains($this->string, $needle);
	}

	public function containsAll(array $needles): bool
	{
		return Text::containsAll($this->string, $needles);
	}

	public function containsAny(array $needles): bool
	{
		return Text::containsAny($this->string, $needles);
	}

	public function containsNone(array $needles): bool
	{
		return Text::containsNone($this->string, $needles);
	}

	public function decrement(string $separator = '-', int $step = 1): string
	{
		$this->string = Text::decrement($this->string, $separator, $step);
		return $this;
	}

	public function dirname(int $levels = 1): FluentString
	{
		$this->string = Text::dirname($this->string, $levels);
		return $this;
	}

	public function endsWith(Stringable|string $needle): bool
	{
		return Text::endsWith($this->string, $needle);
	}

	public function endsWithAny(array $needles): bool
	{
		return Text::endsWithAny($this->string, $needles);
	}

	public function endsWithNone(array $needles): bool
	{
		return Text::endsWithNone($this->string, $needles);
	}

	public function explode(string $char, int $elements = PHP_INT_MAX): array
	{
		return Text::explode($this->string, $char, $elements);
	}

	public function finish(Stringable|string $value): FluentString
	{
		$this->string = Text::finish($this->string, $value);
		return $this;
	}

	public function hash(string $algo = 'md5'): FluentString
	{
		$this->string = Text::hash($this->string, $algo);
		return $this;
	}

	public function increment(string $separator = '-', int $step = 1): FluentString
	{
		$this->string = Text::increment($this->string, $separator, $step);
		return $this;
	}

	public function insert(int $interval, string $character): FluentString
	{
		$this->string = Text::insert($this->string, $interval, $character);
		return $this;
	}

	public function isAscii(): bool
	{
		return Text::isAscii($this->string);
	}

	public function isEmpty(): bool
	{
		return Text::isEmpty($this->string);
	}

	public function isHash(string $algo = 'md5'): bool
	{
		return Text::isHash($this->string, $algo);
	}

	public function isNotEmpty(): bool
	{
		return Text::isNotEmpty($this->string);
	}

	public function isSlug(): bool
	{
		return Text::isSlug($this->string);
	}

	public function kebab(): FluentString
	{
		$this->string = Text::kebab($this->string);
		return $this;
	}

	public function length(): int
	{
		return Text::length($this->string);
	}

	public function limit(int $length, string $marker = ''): FluentString
	{
		$this->string = Text::limit($this->string, 0, $length, $marker);
		return $this;
	}

	public function lower(): FluentString
	{
		$this->string = Text::lower($this->string);
		return $this;
	}

	public function mask(string $replacement, int $index, int|null $length = null): FluentString
	{
		$this->string = Text::mask($this->string, $replacement, $index, $length);
		return $this;
	}

	public function maskEmail(string $replacement, int $preserve = 3): FluentString
	{
		$this->string = Text::maskEmail($this->string, $replacement, $preserve);
		return $this;
	}

	public function match(string $pattern): FluentString
	{
		$pattern = Text::startAndFinish($pattern, '/');
		preg_match($pattern, $this->string, $matches);
		return new FluentString($matches[1] ?? '');
	}

	public function matchAll(string $pattern): Collection
	{
		$pattern = Text::startAndFinish($pattern, '/');
		preg_match($pattern, $this->string, $matches);
		array_shift($matches);
		return new Collection($matches);
	}

	public function matches(string $pattern): bool
	{
		$pattern = Text::startAndFinish($pattern, '/');
		return preg_match($pattern, $this->string) === 1;
	}

	public function merge(Stringable|string|array $values): FluentString
	{
		$this->string = Text::merge($this->string, $values);
		return $this;
	}

	public function modify(Closure $callback): FluentString
	{
		$callback($this);
		return $this;
	}

	public function occurrences(mixed $needle): int
	{
		return Text::occurrences($this->string, $needle);
	}

	public function padBoth(int $length, string $pad_with = ' '): FluentString
	{
		$this->string = Text::padBoth($this->string, $length, $pad_with);
		return $this;
	}

	public function padLeft(int $length, string $pad_with = ' '): FluentString
	{
		$this->string = Text::padLeft($this->string, $length, $pad_with);
		return $this;
	}

	public function padRight(int $length, string $pad_with = ' '): FluentString
	{
		$this->string = Text::padRight($this->string, $length, $pad_with);
		return $this;
	}

	public function pascal(): FluentString
	{
		$this->string = Text::pascal($this->string);
		return $this;
	}

	public function pipe(callable $callback): FluentString
	{
		$result = $callback instanceof Closure ? $callback($this) : $callback($this->string);
		return $result instanceof FluentString ? $result : new FluentString($result);
	}

	public function plural(mixed $count = 2): FluentString
	{
		$this->string = Text::plural($this->string, $count);
		return $this;
	}

	public function prepend(Stringable|string $addition): FluentString
	{
		$this->string = Text::prepend($this->string, $addition);
		return $this;
	}

	public function reverse(): FluentString
	{
		$this->string = Text::reverse($this->string);
		return $this;
	}

	public function remove(Stringable|string|array $values, bool $ignore_case = false): FluentString
	{
		$this->string = Text::remove($this->string, $values, $ignore_case);
		return $this;
	}

	public function replace(Stringable|string|array $targets, Stringable|string|array $values): FluentString
	{
		$this->string = Text::replace($this->string, $targets, $values);
		return $this;
	}

	public function replaceSequential(Stringable|string $target, Stringable|string|array $values): FluentString
	{
		$this->string = Text::replaceSequential($this->string, $target, $values);
		return $this;
	}

	public function replaceFirst(Stringable|string $target, Stringable|string $value): FluentString
	{
		$this->string = Text::replaceFirst($this->string, $target, $value);
		return $this;
	}

	public function replaceLast(Stringable|string $target, Stringable|string $value): FluentString
	{
		$this->string = Text::replaceLast($this->string, $target, $value);
		return $this;
	}

	public function scan(string $format): Collection
	{
		return new Collection(Text::scan($this->string, $format));
	}

	public function scramble(): FluentString
	{
		$this->string = Text::scramble($this->string);
		return $this;
	}

	public function shuffle(): FluentString
	{
		$this->string = Text::shuffle($this->string);
		return $this;
	}

	public function simplify(): FluentString
	{
		$this->string = Text::simplify($this->string);
		return $this;
	}

	public function slug(string $separator = '-'): FluentString
	{
		$this->string = Text::slug($this->string, $separator);
		return $this;
	}

	public function snake(string $separator = '_'): FluentString
	{
		$this->string = Text::snake($this->string, $separator);
		return $this;
	}

	public function start(Stringable|string $value): FluentString
	{
		$this->string = Text::start($this->string, $value);
		return $this;
	}

	public function startAndFinish(Stringable|string $value): FluentString
	{
		$this->string = Text::startAndFinish($this->string, $value);
		return $this;
	}

	public function startsWith(Stringable|string $needle): bool
	{
		return Text::startsWith($this->string, $needle);
	}

	public function startsWithAny(array $needles): bool
	{
		return Text::startsWithAny($this->string, $needles);
	}

	public function startsWithNone(array $needles): bool
	{
		return Text::startsWithNone($this->string, $needles);
	}

	public function swap(array $map): FluentString
	{
		$this->string = Text::swap($this->string, $map);
		return $this;
	}

	public function tap(callable $callback): FluentString
	{
		$callback($this);
		return $this;
	}

	public function title(): FluentString
	{
		$this->string = Text::title($this->string);
		return $this;
	}

	public function trim(string|null $characters = null): FluentString
	{
		$this->string = Text::trim($this->string, $characters);
		return $this;
	}

	public function trimLeft(string|null $characters = null): FluentString
	{
		$this->string = Text::trimLeft($this->string, $characters);
		return $this;
	}

	public function trimRight(string|null $characters = null): FluentString
	{
		$this->string = Text::trimRight($this->string, $characters);
		return $this;
	}

	public function upper(): FluentString
	{
		$this->string = Text::upper($this->string);
		return $this;
	}

	public function whenEmpty(callable $callback, callable|null $alternative = null): FluentString
	{
		return $this->when($this->isEmpty(), $callback, $alternative);
	}

	public function whenNotEmpty(callable $callback, callable|null $alternative = null): FluentString
	{
		return $this->when($this->isNotEmpty(), $callback, $alternative);
	}

	public function whenMatches(string $pattern, callable $callback, callable|null $alternative = null): FluentString
	{
		$pattern = Text::startAndFinish($pattern, '/');
		return $this->when(preg_match($pattern, $this->string) === 1, $callback, $alternative);
	}

	public function wordCount(): int
	{
		return Text::wordCount($this->string);
	}

	public function wrap(string $value, string|null $end = null): FluentString
	{
		$this->string = Text::wrap($this->string, $value, $end);
		return $this;
	}

}