<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Auth\Traits;

use Rovota\Core\Auth\AccessManager;
use Rovota\Core\Auth\Permission;
use Rovota\Core\Structures\Bucket;

trait Permissions
{

	/**
	 * @var Bucket<string, Permission>
	 */
	public Bucket $permissions;

	// -----------------

	public function setPermission(Permission|string|int $identifier, bool $save = false): void
	{
		if (is_string($identifier) || is_int($identifier)) {
			$identifier = AccessManager::getOrLoadPermission($identifier);
		}

		$this->permissions->set($identifier->name, $identifier);
		$this->permission_list = $this->permissions->pluck('id')->toArray();

		if ($save) {
			$this->save();
		}
	}

	public function hasPermission(array|string|int $identifiers): bool
	{
		if ($this->permissions->isEmpty()) {
			return false;
		}

		$identifiers = is_array($identifiers) ? $identifiers : [$identifiers];
		foreach ($identifiers as $identifier) {
			if ($this->getPermission($identifier) !== null) {
				return true;
			}
		}

		return false;
	}

	public function hasAllPermissions(array $identifiers): bool
	{
		if ($this->permissions->isEmpty()) {
			return false;
		}

		foreach ($identifiers as $identifier) {
			if ($this->getPermission($identifier) === null) {
				return false;
			}
		}

		return true;
	}

	public function getPermission(string|int $identifier): Permission|null
	{
		if (is_int($identifier)) {
			$identifier = AccessManager::getPermission($identifier)?->name;
		}
		return $this->permissions->get($identifier);
	}

	public function getPermissions(): Bucket
	{
		return $this->permissions;
	}

	// -----------------

	protected function loadPermissions(): void
	{
		$permissions_denied = [];
		if (property_exists($this, 'permissions_denied')) {
			$permissions_denied = $this->permissions_denied;
		}

		foreach ($this->permission_list ?? [] as $identifier) {
			if (in_array($identifier, $permissions_denied) === false) {
				$permission = AccessManager::getOrLoadPermission($identifier);
				$this->permissions[$permission->name] = $permission;
			}
		}
	}

	// -----------------

	protected function preparePermissions(): void
	{
		$this->permissions = new Bucket();
	}

}