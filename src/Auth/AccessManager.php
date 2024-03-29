<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Auth;

use Rovota\Core\Cookie\Cookie;
use Rovota\Core\Cookie\CookieManager;
use Rovota\Core\Facades\Registry;
use Rovota\Core\Http\RequestManager;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Enums\Status;
use Rovota\Core\Support\Str;
use Throwable;

final class AccessManager
{

	/**
	 * @var Bucket<int, Permission>
	 */
	protected static Bucket $permissions;

	protected static array $permission_map = [];

	/**
	 * @var Bucket<int, Role>
	 */
	protected static Bucket $roles;

	protected static array $role_map = [];

	protected static string $csrf_token;

	// -----------------

	protected function __construct()
	{
	}

	// -----------------

	/**
	 * @internal
	 */
	public static function initialize(): void
	{
		self::$permissions = new Bucket();
		self::$roles = new Bucket();

		self::initializeCsrfToken();
	}

	// -----------------
	// Permissions

	public static function loadPermission(string|int $identifier): void
	{
		try {
			$permission = Permission::where(['status' => Status::Enabled])->find($identifier, is_numeric($identifier) ? 'id' : 'name');
			if ($permission instanceof Permission) {
				self::$permissions[$permission->id] = $permission;
				self::$permission_map[$permission->name] = $permission->id;
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable, true);
		}
	}

	public static function rememberPermission(Permission $permission): void
	{
		self::$permissions[$permission->id] = $permission;
		self::$permission_map[$permission->name] = $permission->id;
	}

	public static function knowsPermission(string|int $identifier): bool
	{
		return self::findPermissionByIdentifier($identifier) instanceof Permission;
	}

	public static function getPermission(string|int $identifier): Permission|null
	{
		return self::findPermissionByIdentifier($identifier);
	}

	public static function getOrLoadPermission(string|int $identifier): Permission|null
	{
		if (self::knowsPermission($identifier) === false) {
			self::loadPermission($identifier);
		}
		return self::findPermissionByIdentifier($identifier);
	}

	/**
	 * @returns Bucket<int, Permission>
	 */
	public static function getPermissions(): Bucket
	{
		return self::$permissions;
	}

	// -----------------
	// Roles

	public static function loadRole(string|int $identifier): void
	{
		try {
			$role = Role::where(['status' => Status::Enabled])->find($identifier, is_numeric($identifier) ? 'id' : 'name');
			if ($role instanceof Role) {
				self::$roles[$role->id] = $role;
				self::$role_map[$role->name] = $role->id;
			}
		} catch (Throwable $throwable) {
			ExceptionHandler::addThrowable($throwable, true);
		}
	}

	public static function rememberRole(Role $role): void
	{
		self::$roles[$role->id] = $role;
		self::$role_map[$role->name] = $role->id;
	}

	public static function knowsRole(string|int $identifier): bool
	{
		return self::findRoleByIdentifier($identifier) instanceof Role;
	}

	public static function getRole(string|int $identifier): Role|null
	{
		return self::findRoleByIdentifier($identifier);
	}

	public static function getOrLoadRole(string|int $identifier): Role|null
	{
		if (self::knowsRole($identifier) === false) {
			self::loadRole($identifier);
		}
		return self::findRoleByIdentifier($identifier);
	}

	/**
	 * @returns Bucket<int, Role>
	 */
	public static function getRoles(): Bucket
	{
		return self::$roles;
	}

	// -----------------
	// CSRF

	public static function getCsrfToken(): string
	{
		return self::$csrf_token;
	}

	public static function getCsrfTokenName(): string
	{
		return Registry::string('security_csrf_token', 'csrf_protection_token');
	}

	public static function verifyCsrfToken(string|null $token = null): bool
	{
		if ($token === null) {
			$token = RequestManager::getRequest()->post->get(self::getCsrfTokenName());
		}
		return self::$csrf_token === $token;
	}

	// -----------------
	// Internal

	protected static function findPermissionByIdentifier(string|int $identifier): Permission|null
	{
		if (is_string($identifier) && isset(self::$permission_map[$identifier])) {
			$identifier = self::$permission_map[$identifier];
		}
		return self::$permissions[$identifier] ?? null;
	}

	protected static function findRoleByIdentifier(string|int $identifier): Role|null
	{
		if (is_string($identifier) && isset(self::$role_map[$identifier])) {
			$identifier = self::$role_map[$identifier];
		}
		return self::$roles[$identifier] ?? null;
	}

	protected static function initializeCsrfToken(): void
	{
		$token_name = self::getCsrfTokenName();

		$cookie = CookieManager::findReceived($token_name);
		if ($cookie instanceof Cookie) {
			self::$csrf_token = $cookie->value;
			return;
		}

		$token_value = Str::random(80);
		self::$csrf_token = $token_value;
	}

}