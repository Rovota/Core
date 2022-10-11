<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Auth;

use Rovota\Core\Auth\Enums\TwoFactorType;
use Rovota\Core\Database\Model;
use Rovota\Core\Facades\Crypt;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Security\TimeBasedOTP;
use Rovota\Core\Support\Enums\Status;
use Rovota\Core\Support\Moment;
use Throwable;

/**
 * @property int $id
 * @property int $user_id
 * @property TwoFactorType $type
 * @property mixed $content
 * @property int|null $otp_digits
 * @property string|null $otp_digest
 * @property int|null $otp_period
 * @property bool $is_default
 * @property bool $encrypted
 * @property Status $status
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 *
 * @property-read string $label Retrieves the label associated with $type
 * @property-read string $description Retrieves the description associated with $type
 */
class TwoFactorMethod extends Model
{

	protected string|null $table = 'two_factor_methods';

	protected array $attributes = [
		'is_default' => false,
		'encrypted' => true,
		'status' => Status::Enabled,
		'type' => TwoFactorType::App,
	];

	protected array $casts = [
		'is_default' => 'bool',
		'encrypted' => 'bool',
		'status' => ['enum', Status::class],
		'type' => ['enum', TwoFactorType::class],
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	// -----------------

	protected function getLabelAttribute(): string
	{
		return $this->type->label();
	}

	protected function getDescriptionAttribute(): string
	{
		return $this->type->description();
	}

	// -----------------

	public function verify(mixed $input): bool
	{
		$content = $this->getContent() ?? '';

		return match($this->type) {
			TwoFactorType::App => $this->verifyApp($content, $input),
			TwoFactorType::Recovery => $this->verifyRecovery($content, $input),
			default => false,
		};
	}

	// -----------------

	protected function verifyApp(string $secret, string $code): bool
	{
		$authenticator = TimeBasedOTP::createWith($secret, $this->otp_period ?? 30, $this->otp_digest ?? 'sha1', $this->otp_digits ?? 6);
		return $authenticator->verify($code);
	}

	// -----------------

	protected function verifyRecovery(string $codes, string $code): bool
	{
		$codes = explode(',', $codes);
		foreach ($codes as $key => $value) {
			if ($value === $code) {
				unset($codes[$key]);
				$this->setContent(implode(',', $codes));
				$this->save();
				return true;
			}
		}
		return false;
	}

	public function setRecoveryCodes(int $amount = 6): void
	{
		$iteration = 0;
		$codes = [];
		while ($iteration < $amount) {
			try {
				$codes[] = random_int(100000, 999999);
			} catch (Throwable) { }
			$iteration++;
		}
		$this->setContent(implode(',', $codes));
	}

	// -----------------

	private function getContent(): string|null
	{
		try {
			return $this->encrypted ? Crypt::decryptString($this->content) : $this->content;
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable, true);
			return null;
		}
	}

	private function setContent(string $content): void
	{
		try {
			$this->content = $this->encrypted ? Crypt::encryptString($content) : $content;
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable, true);
		}
	}

}