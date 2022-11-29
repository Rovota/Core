<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support;

use JsonSerializable;
use PHLAK\SemVer\Exceptions\InvalidVersionException;
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

	public function incrementMajor(): Version
	{
		$this->semver->incrementMajor();
		return $this;
	}

	public function incrementMinor(): Version
	{
		$this->semver->incrementMinor();
		return $this;
	}

	public function incrementPatch(): Version
	{
		$this->semver->incrementPatch();
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

	public function isGreater(Version|string $version): bool
	{
		$version = $this->getInstance($version);
		return !($version === null) && $this->semver->gt($version->semver());
	}

	public function isLower(Version|string $version): bool
	{
		$version = $this->getInstance($version);
		return !($version === null) && $this->semver->lt($version->semver());
	}

	public function isEqual(Version|string $version): bool
	{
		$version = $this->getInstance($version);
		return !($version === null) && $this->semver->eq($version->semver());
	}

	public function isNotEqual(Version|string $version): bool
	{
		$version = $this->getInstance($version);
		return !($version === null) && $this->semver->neq($version->semver());
	}

	public function isEqualOrGreater(Version|string $version): bool
	{
		$version = $this->getInstance($version);
		return !($version === null) && $this->semver->gte($version->semver());
	}

	public function isEqualOrLower(Version|string $version): bool
	{
		$version = $this->getInstance($version);
		return !($version === null) && $this->semver->lte($version->semver());
	}

	// -----------------

	public function semVer(): SemVer
	{
		return $this->semver;
	}

	protected function getInstance(Version|string $version): Version|null
	{
		try {
			$version = is_string($version) ? new Version($version) : $version;
		} catch(InvalidVersionException) {
			return null;
		}
		return $version;
	}

}