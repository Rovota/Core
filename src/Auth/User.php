<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Auth;

use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Auth\Traits\Permissions;
use Rovota\Core\Cache\CacheManager;
use Rovota\Core\Database\Model;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Localization\Language;
use Rovota\Core\Localization\LocalizationManager;
use Rovota\Core\Security\Hash;
use Rovota\Core\Support\Enums\Status;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Traits\Metadata;
use Throwable;
use function now;

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
 * @property array|null $permission_list
 * @property array|null $permissions_denied
 * @property Moment|null $last_active
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class User extends Model implements Identity
{
	use Permissions, Metadata;

	protected string|null $table = 'users';

	protected array $attributes = [
		'email_verified' => false,
		'role_id' => 1,
	];

	protected array $casts = [
		'email_verified' => 'bool',
		'permission_list' => 'array',
		'permissions_denied' => 'array',
		'last_active' => 'moment',
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	protected bool $enable_composites = false;

	protected string $meta_model = UserMeta::class;
	protected string $meta_foreign_key = 'user_id';

	// -----------------

	public Suspension|null $suspension = null;
	public Language|null $language = null;
	public Role|null $role = null;

	protected array $twofactor_methods = [];
	protected string|null $twofactor_default = null;

	// -----------------

	public function eventModelLoaded(): void
	{
		$this->loadLanguage();
		$this->loadSuspension();
		$this->loadPermissions();
		$this->loadRole();
		$this->loadMeta();
		$this->loadTwoFactorMethods();
	}

	// -----------------
	// Generic

	public function getName(): string
	{
		return $this->username;
	}

	public function getDisplayName(): string
	{
		return $this->nickname;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	// -----------------
	// Language

	public function setLanguage(Language|string|int $identifier, bool $save = false): void
	{
		if (is_string($identifier) || is_int($identifier)) {
			$identifier = LocalizationManager::getLanguage($identifier);
		}

		$this->language = $identifier;
		$this->language_id = $identifier->id;

		if ($save) {
			$this->save();
		}
	}

	public function getLanguage(): Language|null
	{
		return $this->language;
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
	// Role

	public function setRole(Role|string|int $identifier, bool $save = false): void
	{
		if (is_string($identifier) || is_int($identifier)) {
			$identifier = AccessManager::getOrLoadRole($identifier);
		}

		$this->role = $identifier;
		$this->role_id = $identifier->id;

		if ($save) {
			$this->save();
		}
	}

	public function hasRole(array|string|int $identifiers): bool
	{
		if ($this->role === null) {
			return false;
		}

		$identifiers = is_array($identifiers) ? $identifiers : [$identifiers];
		foreach ($identifiers as $identifier) {
			if (is_int($identifier) ? $this->role->id === $identifier: $this->role->name === $identifier) {
				return true;
			}
		}

		return false;
	}

	public function getRole(): Role|null
	{
		return $this->role;
	}

	// -----------------
	// Last Active

	public function updateLastActive(): void
	{
		$key = 'last_active_hit:'.$this->getId();
		if (CacheManager::get()->has($key) === false) {
			$this->last_active = now();
			$this->save();
			CacheManager::get()->set($key, now()->toDateTimeString(), 300);
		}
	}

	public function getLastActive(): Moment
	{
		$key = 'last_active_hit:'.$this->getId();
		return CacheManager::get()->get($key, $this->created);
	}

	public function isOnline(int $minutes = 5): bool
	{
		if ($this->getLastActive()->diffInMinutes() <= $minutes) {
			return true;
		}
		return false;
	}

	public function isActive(int $days = 365): bool
	{
		if ($this->getLastActive()->diffInDays() <= $days) {
			return true;
		}
		return false;
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

	protected function loadLanguage(): void
	{
		if (LocalizationManager::hasLanguage($this->language_id)) {
			$this->language = LocalizationManager::getLanguage($this->language_id);
		} else {
			$this->language = LocalizationManager::getActiveLanguage();
		}
	}

	protected function loadRole(): void
	{
		$this->role = AccessManager::getOrLoadRole($this->role_id);
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