<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support\Traits;

use DateTimeInterface;

trait MomentFormatters
{

	protected string $default_format = 'Y-m-d H:i:s';

	// -----------------

	public function format(string $format, bool $to_local_tz = false): string
	{
		if ($to_local_tz) {
			$this->setTimezoneLocal();
		}
		return parent::format($format);
	}

	public function jsonSerialize(): string
	{
		return $this->toAtomString();
	}

	// -----------------

	public function toEpochString(): string
	{
		return $this->format('U');
	}

	public function toDateString(): string
	{
		return $this->format('Y-m-d');
	}

	public function toFormattedDateString(): string
	{
		return $this->format('M d, Y');
	}

	public function toTimeString(): string
	{
		return $this->format('H:i:s');
	}

	public function toDateTimeString(): string
	{
		return $this->format('Y-m-d H:i:s');
	}

	public function toAtomString(): string
	{
		return $this->format(DateTimeInterface::ATOM);
	}

	public function toCookieString(): string
	{
		return $this->format(DateTimeInterface::COOKIE);
	}

	public function toRfc822String(): string
	{
		return $this->format(DateTimeInterface::RFC822);
	}

	public function toRfc850String(): string
	{
		return $this->format(DateTimeInterface::RFC850);
	}

	public function toRfc1036String(): string
	{
		return $this->format(DateTimeInterface::RFC1036);
	}

	public function toRfc1123String(): string
	{
		return $this->format(DateTimeInterface::RFC1123);
	}

	public function toRfc2822String(): string
	{
		return $this->format(DateTimeInterface::RFC2822);
	}

	public function toRfc3339String(): string
	{
		return $this->format(DateTimeInterface::RFC3339);
	}

	public function toRfc7231String(): string
	{
		return $this->format(DateTimeInterface::RFC7231);
	}

	public function toRssString(): string
	{
		return $this->format(DateTimeInterface::RSS);
	}

	public function toW3cString(): string
	{
		return $this->format(DateTimeInterface::W3C);
	}

	// -----------------

	public function toLocalFormat(string $format): string
	{
		return $this->format($format, true);
	}

	public function toLocalEpochString(): string
	{
		return $this->format('U', true);
	}

	public function toLocalDateString(): string
	{
		return $this->format('Y-m-d', true);
	}

	public function toLocalFormattedDateString(): string
	{
		return $this->format('M d, Y', true);
	}

	public function toLocalTimeString(): string
	{
		return $this->format('H:i:s', true);
	}

	public function toLocalDateTimeString(): string
	{
		return $this->format('Y-m-d H:i:s', true);
	}

	public function toLocalAtomString(): string
	{
		return $this->format(DateTimeInterface::ATOM, true);
	}

	public function toLocalCookieString(): string
	{
		return $this->format(DateTimeInterface::COOKIE, true);
	}

	public function toLocalRfc822String(): string
	{
		return $this->format(DateTimeInterface::RFC822, true);
	}

	public function toLocalRfc850String(): string
	{
		return $this->format(DateTimeInterface::RFC850, true);
	}

	public function toLocalRfc1036String(): string
	{
		return $this->format(DateTimeInterface::RFC1036, true);
	}

	public function toLocalRfc1123String(): string
	{
		return $this->format(DateTimeInterface::RFC1123, true);
	}

	public function toLocalRfc2822String(): string
	{
		return $this->format(DateTimeInterface::RFC2822, true);
	}

	public function toLocalRfc3339String(): string
	{
		return $this->format(DateTimeInterface::RFC3339, true);
	}

	public function toLocalRfc7231String(): string
	{
		return $this->format(DateTimeInterface::RFC7231, true);
	}

	public function toLocalRssString(): string
	{
		return $this->format(DateTimeInterface::RSS, true);
	}

	public function toLocalW3cString(): string
	{
		return $this->format(DateTimeInterface::W3C, true);
	}

	// -----------------

	public function toUtcFormat(string $format): string
	{
		$this->setTimezoneUtc();
		return $this->format($format);
	}

	public function toUtcEpochString(): string
	{
		$this->setTimezoneUtc();
		return $this->format('U');
	}

	public function toUtcDateString(): string
	{
		$this->setTimezoneUtc();
		return $this->format('Y-m-d');
	}

	public function toUtcFormattedDateString(): string
	{
		$this->setTimezoneUtc();
		return $this->format('M d, Y');
	}

	public function toUtcTimeString(): string
	{
		$this->setTimezoneUtc();
		return $this->format('H:i:s');
	}

	public function toUtcDateTimeString(): string
	{
		$this->setTimezoneUtc();
		return $this->format('Y-m-d H:i:s');
	}

	public function toUtcAtomString(): string
	{
		$this->setTimezoneUtc();
		return $this->format(DateTimeInterface::ATOM, true);
	}

	public function toUtcCookieString(): string
	{
		$this->setTimezoneUtc();
		return $this->format(DateTimeInterface::COOKIE, true);
	}

	public function toUtcRfc822String(): string
	{
		$this->setTimezoneUtc();
		return $this->format(DateTimeInterface::RFC822, true);
	}

	public function toUtcRfc850String(): string
	{
		$this->setTimezoneUtc();
		return $this->format(DateTimeInterface::RFC850, true);
	}

	public function toUtcRfc1036String(): string
	{
		$this->setTimezoneUtc();
		return $this->format(DateTimeInterface::RFC1036, true);
	}

	public function toUtcRfc1123String(): string
	{
		$this->setTimezoneUtc();
		return $this->format(DateTimeInterface::RFC1123, true);
	}

	public function toUtcRfc2822String(): string
	{
		$this->setTimezoneUtc();
		return $this->format(DateTimeInterface::RFC2822, true);
	}

	public function toUtcRfc3339String(): string
	{
		$this->setTimezoneUtc();
		return $this->format(DateTimeInterface::RFC3339, true);
	}

	public function toUtcRfc7231String(): string
	{
		$this->setTimezoneUtc();
		return $this->format(DateTimeInterface::RFC7231, true);
	}

	public function toUtcRssString(): string
	{
		$this->setTimezoneUtc();
		return $this->format(DateTimeInterface::RSS, true);
	}

	public function toUtcW3cString(): string
	{
		$this->setTimezoneUtc();
		return $this->format(DateTimeInterface::W3C, true);
	}

}