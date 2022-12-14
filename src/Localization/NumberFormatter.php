<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Localization;

final class NumberFormatter extends Formatter
{

	protected int $decimals = 2;

	protected string $format = 'default';

	// -----------------

	public function __toString(): string
	{
		return $this->asNumber();
	}

	public function jsonSerialize(): string
	{
		return $this->asNumber();
	}

	// -----------------

	public function decimals(int $decimals): NumberFormatter
	{
		$this->decimals = $decimals;
		return $this;
	}

	public function format(string $format): NumberFormatter
	{
		$this->format = $format;
		return $this;
	}

	// -----------------

	public function asNumber(int|null $decimals = null, string|null $format = null): string
	{
		$format = $format ?? $this->format;
		$decimals = $decimals ?? $this->decimals;

		if ($this->formats->has('number.format.'.$format) === false) {
			$format = 'default';
		}

		$callback = $this->formats->get('number.format.'.$format);
		return $callback($this->input, $decimals);
	}

	public function asCapacity(int|null $decimals = null, string|null $format = null): string
	{
		$format = $format ?? $this->format;
		$decimals = $decimals ?? $this->decimals;

		$suffixes = $this->formats->get('storage.unit.short');
		$class = min((int)log($this->input, 1024), count($suffixes) - 1);
		$result = [
			'value' => round($this->input / pow(1024, $class), $decimals),
			'suffix' => $suffixes[$class],
		];

		$result['value'] = self::create($result['value'], $this->locale)->asNumber($decimals, $format);
		return sprintf('%s %s', $result['value'], $result['suffix']);
	}

}