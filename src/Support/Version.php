<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Support;

use JsonSerializable;
use PHLAK\SemVer\Version as SemVer;

final class Version extends SemVer implements JsonSerializable
{

	public function __construct(string $version = '0.1.0')
	{
		parent::__construct($version);
	}

	// -----------------

	public function __toString(): string
	{
		return $this->full();
	}

	public function jsonSerialize(): string
	{
		return $this->full();
	}

	// -----------------

	public function basic(): string
	{
		$version = implode('.', [$this->major, $this->minor, $this->patch]);
		if (empty($this->preRelease) === false) {
			$version .= '-'.$this->preRelease;
		}
		return $version;
	}

	public function full(): string
	{
		$version = $this->basic();
		if (empty($this->build) === false) {
			$version .= '+'.$this->build;
		}
		return $version;
	}

	public function format(string $format): string
	{
		$matches = [];
		if (preg_match_all('#\{([a-z\d_]*)}#m', $format, $matches) > 0) {
			foreach ($matches[1] as $element) {
				if (method_exists($this, $element)) {
					$format = str_replace('{'.$element.'}', $this->{$element}(), $format);
				}
			}
		}
		return $format;
	}

	// -----------------

	public function major(): int
	{
		return $this->major;
	}

	public function minor(): int
	{
		return $this->minor;
	}

	public function patch(): int
	{
		return $this->patch;
	}

	public function preRelease(): string|null
	{
		return $this->preRelease;
	}

	public function build(): string|null
	{
		return $this->build;
	}

	// -----------------

	public function isGreater($version): bool
	{
		return $this->gt($version);
	}

	public function isLower($version): bool
	{
		return $this->lt($version);
	}

	public function isEqual($version): bool
	{
		return $this->eq($version);
	}

	public function isNotEqual($version): bool
	{
		return $this->neq($version);
	}

	public function isEqualOrGreater($version): bool
	{
		return $this->gte($version);
	}

	public function isEqualOrLower($version): bool
	{
		return $this->lte($version);
	}

}