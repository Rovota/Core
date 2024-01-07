<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Access\Features;

use Rovota\Core\Database\Model;
use Rovota\Core\Support\Moment;

/**
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string $description
 * @property bool $enabled
 * @property string $variant
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class FeatureDbEntry extends Model
{

	protected string|null $table = 'core_features';

	protected array $attributes = [
		'enabled' => false,
	];

	protected array $casts = [
		'enabled' => 'bool',
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	protected bool $enable_composites = false;

}