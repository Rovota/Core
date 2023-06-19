<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Kernel;

use Rovota\Core\Database\Model;
use Rovota\Core\Support\Moment;

/**
 * @property int $id
 * @property string $name
 * @property string $value
 * @property string $vendor
 * @property bool $protected
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
final class RegistryOption extends Model
{

	protected string|null $table = '_registry';

	protected array $attributes = [
		'protected' => false,
	];

	protected array $casts = [
		'protected' => 'bool',
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	protected bool $enable_composites = false;

}