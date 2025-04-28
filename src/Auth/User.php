<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Auth;

use Rovota\Core\Auth\Interfaces\Identity;
use Throwable;

/**
 * @property int $id
 * @property string $username
 * @property string $slugname
 * @property string $nickname
 * @property string $email
 * @property string|null $email_recovery
 * @property bool $email_verified
 * @property string $password
 * @property int $language_id
 * @property int $role_id
 * @property array $permission_list
 * @property array $permissions_denied
 * @property Moment|null $last_active
 * @property Status $status
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class User extends Model implements Identity
{

	public Suspension|null $suspension = null;

	protected array $twofactor_methods = [];
	protected string|null $twofactor_default = null;

	// -----------------

	public function eventModelLoaded(): void
	{
		$this->loadSuspension();
		$this->loadTwoFactorMethods();
	}

	// -----------------
	// Suspension

	public function setSuspension(array $attributes = []): void
	{
		$suspension = Suspension::createUsing($this, $attributes);
		if ($suspension->save()) {
			$this->suspension = $suspension;
		}
	}

	public function isSuspended(): bool
	{
		return $this->suspension instanceof Suspension;
	}

	public function getSuspension(): Suspension|null
	{
		return $this->suspension;
	}

	// -----------------
	// Two Factor

	public function hasDefaultTwoFactorMethod(): bool
	{
		return $this->twofactor_default !== null;
	}

	public function getDefaultTwoFactorMethod(): TwoFactorMethod|null
	{
		return $this->getTwoFactorMethod($this->twofactor_default ?? '');
	}

	public function hasTwoFactorMethod(string $type): bool
	{
		return isset($this->twofactor_methods[$type]);
	}

	public function hasTwoFactorMethods(): bool
	{
		return empty($this->twofactor_methods) === false;
	}

	public function getTwoFactorMethod(string $type): TwoFactorMethod|null
	{
		return $this->twofactor_methods[$type] ?? null;
	}

	public function getTwoFactorMethods(): array
	{
		return $this->twofactor_methods;
	}

	public function verifyTwoFactorMethod(string $type, mixed $input): bool
	{
		if (isset($this->twofactor_methods[$type]) === false) {
			return false;
		}
		return $this->twofactor_methods[$type]->verify($input);
	}

	// -----------------
	// Password

	public function setPassword(string $password, bool $save = false): void
	{
		$this->password = Hash::make($password);
		if ($save) {
			$this->save();
		}
	}

	public function verifyPassword(string $input): bool
	{
		if (Hash::verify($input, $this->password)) {
			if (Hash::needsRehash($this->password)) {
				$this->setPassword($input);
			}
			return true;
		}
		return false;
	}

	// -----------------
	// Internals

	protected function loadSuspension(): void
	{
		try {
			$suspension = Suspension::where(['user_id' => $this->getId()])->first();
			if ($suspension instanceof Suspension) {
				if ($suspension->expiration === null || $suspension->expiration->isFuture()) {
					$this->suspension = $suspension;
				}
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
		}
	}

	protected function loadTwoFactorMethods(): void
	{
		try {
			$methods = TwoFactorMethod::where(['user_id' => $this->getId(), 'status' => Status::Enabled])->getBy('type');
			foreach ($methods as $type => $method) {
				$this->twofactor_methods[$type] = $method;
				if ($method->is_default) {
					$this->twofactor_default = $type;
				}
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable);
		}
	}

	// -----------------
	// Events

	public function eventAuthenticated(): void
	{
		$this->updateLastActive();
	}

}