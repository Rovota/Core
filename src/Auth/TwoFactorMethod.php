<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Auth;

use Exception;
use Rovota\Core\Auth\Enums\TwoFactorType;
use Rovota\Core\Auth\Interfaces\MailSupportsCode;
use Rovota\Core\Database\Model;
use Rovota\Core\Facades\Crypt;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Mail\Interfaces\Mailable;
use Rovota\Core\Security\TimeBasedOTP;
use Rovota\Core\Session\SessionManager;
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

	public function prepare(array $data = []): bool
	{
		return match($this->type) {
			TwoFactorType::Email => $this->prepareEmail($data['email_class']),
			default => true,
		};
	}

	public function verify(mixed $input): bool
	{
		return match($this->type) {
			TwoFactorType::App => $this->verifyApp($input),
			TwoFactorType::Recovery => $this->verifyRecovery($input),
			TwoFactorType::Email => $this->verifyEmail($input),
			default => false,
		};
	}

	// -----------------

	protected function verifyApp(string $input): bool
	{
		$authenticator = TimeBasedOTP::createWith($this->getContent() ?? '', $this->otp_period ?? 30, $this->otp_digest ?? 'sha1', $this->otp_digits ?? 6);
		return $authenticator->verify($input);
	}

	// -----------------

	protected function verifyRecovery(string $input): bool
	{
		$codes = explode(',', $this->getContent() ?? '');
		foreach ($codes as $key => $value) {
			if ($value === $input) {
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
			$codes[] = $this->generateCode();
		}
		$this->setContent(implode(',', $codes));
	}

	// -----------------

	protected function prepareEmail(string $email_class): bool
	{
		$timestamp = SessionManager::get()->read('2fa_mail_timestamp');
		if ($timestamp instanceof Moment && $timestamp->diffInMinutes() < 5) {
			return true;
		}

		try {
			$user = User::find($this->user_id);
		} catch (Exception) {
			return false;
		}

		$code = $this->generateCode();
		SessionManager::get()->put('2fa_mail_code', $code);
		SessionManager::get()->put('2fa_mail_timestamp', now());

		/**
		 * @var Mailable&MailSupportsCode $email_class
		 */
		$email = new $email_class();
		$email->to($user);
		$email->code($code);

		return $email->deliver();
	}

	protected function verifyEmail(string $input): bool
	{
		$reference = SessionManager::get()->read('2fa_mail_code');

		if ($reference === $input) {
			SessionManager::get()->forget('2fa_mail_code');
			SessionManager::get()->forget('2fa_mail_timestamp');
			return true;
		}

		return false;
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

	// -----------------

	protected function generateCode(int $length = 6): string
	{
		$iteration = 0;
		$code = '';
		while ($iteration < $length) {
			try {
				$code .= random_int(0, 9);
			} catch (Throwable) { }
			$iteration++;
		}

		return strlen($code) < $length ? '458676' : $code;
	}

}