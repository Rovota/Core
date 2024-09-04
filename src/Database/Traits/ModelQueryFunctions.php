<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Traits;

use Envms\FluentPDO\Exception;

trait ModelQueryFunctions
{

	public static function withDeleted(): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->withDeleted();
	}

	public static function onlyDeleted(): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->onlyDeleted();
	}

	// -----------------
	// Delete

	/**
	 * @throws Exception
	 */
	public static function destroy(array|string|int $identifiers, string|null $column = null, bool $permanent = false): int
	{
		$model = new static;
		$column = $column ?? $model->getPrimaryKey();
		$identifiers = is_array($identifiers) ? $identifiers : [$identifiers];
		$models = $model::whereIn($column, $identifiers)->get();

		/**
		 * @var Model $model;
		 */
		foreach ($models as $model) {
			$permanent ? $model->eventModelForceDeleted() : $model->eventModelSoftDeleted();
		}

		$builder = (new static)->newQueryBuilder();
		return $builder->whereIn($column, $identifiers)->delete($permanent);
	}
}