<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Support;

use Rovota\Core\Database\CastManager;
use Rovota\Core\Database\Model;
use Rovota\Core\Kernel\Resolver;
use stdClass;

/**
 * @property int $id
 * @property string $name
 * @property mixed $value
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
abstract class Meta extends Model
{

	protected array $restricted = [
		'type' => [
			'array',
			'bool',
			'collection',
			'datetime',
			'float',
			'fluent_string',
			'int',
			'moment',
			'string',
		],
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	// -----------------

	public static function newFromBuilder(stdClass $class): static
	{
		$instance = parent::newFromBuilder($class);

		if ($class->value !== null) {
			$value = CastManager::castFromRaw($class->value, $class->type);
			$instance->forceValueAndCast($value, $class->type);
		}

		return $instance;
	}

	// -----------------

	protected function setValueAttribute(mixed $value): void
	{
		$type = Resolver::getValueType($value);
		if (isset($this->attributes['type']) === false || $this->attributes['type'] !== $type) {
			$this->attributes_modified['type'] = $type;
		}
		$this->attributes_modified['value'] = $value;
	}

	// -----------------

	public function save(): bool
	{
		if (isset($this->attributes_modified['value'])) {

			$type = $this->attributes_modified['type'] ?? $this->attributes['type'];
			$pre_casting = $this->attributes_modified['value'];

			$this->attributes_modified['value'] = CastManager::castToRaw($this->attributes_modified['value'], $type);

			$result = parent::save();
			$this->attributes['value'] = $pre_casting;
			return $result;
		}
		return parent::save();
	}

	public function forceValueAndCast(mixed $value, string $type): void
	{
		$this->casts['value'] = $type;
		$this->attributes['value'] = $value;
	}

}