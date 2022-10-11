<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Validation;

use Rovota\Core\Database\Model;
use Rovota\Core\Support\Enums\Status;
use Rovota\Core\Support\Moment;
use Rovota\Core\Validation\Enums\FilterAction;

/**
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string $description
 * @property array $values
 * @property FilterAction $action
 * @property Status $status
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class Filter extends Model
{

	protected string|null $table = 'filters';

	protected array $attributes = [
		'action' => FilterAction::Block,
		'status' => Status::Enabled,
	];

	protected array $casts = [
		'values' => 'array',
		'action' => ['enum', FilterAction::class],
		'status' => ['enum', Status::class],
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	protected bool $enable_composites = false;

}