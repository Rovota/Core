<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Support;

use JsonSerializable;
use PHLAK\SemVer\Version as SemVer;

final class Version implements JsonSerializable
{

	protected SemVer $semver;

	/**
	 * @throws \PHLAK\SemVer\Exceptions\InvalidVersionException
	 */
	public function __construct(string $version = '0.1.0')
	{
		$this->semver = new SemVer($version);
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

	public function setMajor(int $value): Version
	{
		$this->semver->setMajor($value);
		return $this;
	}

	public function setMinor(int $value): Version
	{
		$this->semver->setMinor($value);
		return $this;
	}

	public function setPatch(int $value): Version
	{
		$this->semver->setPatch($value);
		return $this;
	}

	public function setPreRelease(string|null $value): Version
	{
		$this->semver->setPreRelease($value);
		return $this;
	}

	public function setBuild(string|null $value): Version
	{
		$this->semver->setBuild($value);
		return $this;
	}

	// -----------------

	public function basic(): string
	{
		$version = implode('.', [$this->semver->major, $this->semver->minor, $this->semver->patch]);
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
		return $this->semver->major;
	}

	public function minor(): int
	{
		return $this->semver->minor;
	}

	public function patch(): int
	{
		return $this->semver->patch;
	}

	public function preRelease(): string|null
	{
		return $this->semver->preRelease;
	}

	public function build(): string|null
	{
		return $this->semver->build;
	}

	// -----------------

	public function isGreater($version): bool
	{
		return $this->semver->gt($version);
	}

	public function isLower($version): bool
	{
		return $this->semver->lt($version);
	}

	public function isEqual($version): bool
	{
		return $this->semver->eq($version);
	}

	public function isNotEqual($version): bool
	{
		return $this->semver->neq($version);
	}

	public function isEqualOrGreater($version): bool
	{
		return $this->semver->gte($version);
	}

	public function isEqualOrLower($version): bool
	{
		return $this->semver->lte($version);
	}

	// -----------------

	public function semVer(): SemVer
	{
		return $this->semver;
	}

}