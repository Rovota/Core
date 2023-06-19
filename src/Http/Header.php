<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http;

use Rovota\Core\Database\Model;
use Rovota\Core\Support\Enums\Status;
use Rovota\Core\Support\Moment;

/**
 * @property int $id
 * @property string $name
 * @property string $value
 * @property Status $status
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
final class Header extends Model
{

	protected string|null $table = '_headers';

	protected array $attributes = [
		'status' => Status::Enabled,
	];

	protected array $casts = [
		'status' => ['enum', Status::class],
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	protected bool $enable_composites = false;

}