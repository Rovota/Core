<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Auth\Interfaces;

use Rovota\Core\Auth\Permission;
use Rovota\Core\Auth\Role;
use Rovota\Core\Auth\Suspension;
use Rovota\Core\Localization\Language;
use Rovota\Core\Support\Collection;
use Rovota\Core\Support\Moment;

interface Identity
{

	public function getId(): int|string;

	// -----------------
	// Generic

	public function getName(): string;

	public function getEmail(): string;

	// -----------------
	// Language

	public function setLanguage(Language|string|int $identifier, bool $save = false): void;

	public function getLanguage(): Language|null;

	// -----------------
	// Suspension

	public function setSuspension(array $attributes = []): void;

	public function isSuspended(): bool;

	public function getSuspension(): Suspension|null;

	// -----------------
	// Role

	public function setRole(Role|string|int $identifier, bool $save = false): void;

	public function hasRole(array|string|int $identifiers): bool;

	public function getRole(): Role|null;

	// -----------------
	// Last Active

	public function updateLastActive(): void;

	public function getLastActive(): Moment;

	public function isOnline(int $minutes = 5): bool;

	public function isActive(int $days = 365): bool;

	// -----------------
	// Permissions

	public function setPermission(Permission|string|int $identifier, bool $save = false): void;

	public function hasPermission(array|string|int $identifiers): bool;

	public function hasAllPermissions(array $identifiers): bool;

	public function getPermission(string|int $identifier): Permission|null;

	public function getPermissions(): Collection;

	// -----------------
	// Meta

	public function meta(string $name, mixed $default = null): mixed;

	public function setMeta(string $name, mixed $value, bool $delete_if_null = true): bool;

	public function deleteMeta(string $name, bool $permanent = false): bool;

	// -----------------
	// Events

	public function eventAuthenticated(): void;

}