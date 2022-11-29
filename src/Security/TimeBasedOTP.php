<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Security;

use OTPHP\TOTP;
use Rovota\Core\Facades\Cache;

class TimeBasedOTP
{

	protected TOTP|null $authenticator = null;

	// -----------------
	
	public function __construct(string|null $secret, int $period, string $digest, int $digits, int $epoch)
	{
		$this->authenticator = TOTP::create($secret, $period, $digest, $digits, $epoch);
	}

	// -----------------

	public static function create(int $period = 30, string $digest = 'sha1', int $digits = 6, int $epoch = 0): static
	{
		return new static(null, $period, $digest, $digits, $epoch);
	}

	public static function createWith(string $secret, int $period = 30, string $digest = 'sha1', int $digits = 6, int $epoch = 0): static
	{
		return new static($secret, $period, $digest, $digits, $epoch);
	}

	// -----------------

	public function getAuthenticator(): TOTP|null
	{
		return $this->authenticator;
	}

	// -----------------

	public function verify(string $otp, null|int $timestamp = null, null|int $leeway = null): bool
	{
		$accepted = $this->authenticator->verify($otp, $timestamp, $leeway ?? round($this->getPeriod() / 4));

		if ($accepted === true) {
			$key = hash('sha256', $this->getSecret()).'-'.$otp;
			if (Cache::has($key)) {
				return false;
			}
			Cache::put($key, 1, $this->getPeriod());
		}

		return $accepted;
	}

	// -----------------

	public function current(): string
	{
		return $this->authenticator->now();
	}

	public function at(int $timestamp): string
	{
		return $this->authenticator->at($timestamp);
	}

	// -----------------

	public function expiresIn(): int
	{
		return $this->authenticator->expiresIn();
	}

	// -----------------

	public function setLabel(string $label): static
	{
		$this->authenticator->setLabel(trim($label));
		return $this;
	}

	public function setIssuer(string $issuer): static
	{
		$this->authenticator->setIssuer(trim($issuer));
		return $this;
	}

	// -----------------

	public function getSecret(): string
	{
		return $this->authenticator->getSecret();
	}

	public function getDigits(): int
	{
		return $this->authenticator->getDigits();
	}

	public function getDigest(): string
	{
		return $this->authenticator->getDigest();
	}

	public function getPeriod(): int
	{
		return $this->authenticator->getPeriod();
	}

	public function getEpoch(): int
	{
		return $this->authenticator->getEpoch();
	}

	public function getLabel(): string|null
	{
		return $this->authenticator->getLabel();
	}

	public function getIssuer(): string|null
	{
		return $this->authenticator->getIssuer();
	}

	// -----------------

	public function getImageUrl(int $height = 200, int $width = 200): string
	{
		$otp_url = url()->query([
			'secret' => $this->getSecret(),
			'issuer' => $this->getIssuer() ?? registry('site_name'),
		])->external('otpauth://totp/'.$this->getLabel());

		/** @noinspection SpellCheckingInspection */
		return url()->domain('chart.googleapis.com')->query([
			'chs' => $width.'x'.$height, 'chld' => 'M|0', 'cht' => 'qr', 'chl' => $otp_url,
		])->path('chart');
	}

}