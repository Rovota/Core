<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Auth;

use Rovota\Core\Auth\Traits\Permissions;
use Rovota\Core\Database\Model;
use Rovota\Core\Support\Enums\Status;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Traits\Metadata;

/**
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string|null $description
 * @property array $permission_list
 * @property string $section
 * @property bool $protected
 * @property Status $status
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class Role extends Model
{
	use Permissions, Metadata;

	protected string|null $table = '_roles';

	protected array $attributes = [
		'protected' => false,
		'status' => Status::Enabled,
	];

	protected array $casts = [
		'permission_list' => 'array',
		'protected' => 'bool',
		'status' => ['enum', Status::class],
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	protected bool $enable_composites = false;

	// -----------------

	protected string $meta_model = RoleMeta::class;
	protected string $meta_foreign_key = 'role_id';

	// -----------------

	public function eventModelLoaded(): void
	{
		$this->loadPermissions();
		$this->loadMeta();
	}

}