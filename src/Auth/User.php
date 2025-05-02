<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Auth;

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
class User extends Model
{

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

}