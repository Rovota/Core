<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Traits;

use Envms\FluentPDO\Exception;
use Rovota\Core\Database\Builder\Query;
use Rovota\Core\Database\Model;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Interfaces\Arrayable;

trait ModelQueryFunctions
{

	// -----------------
	// Where

	public static function where(string|array $column, mixed $operator = null, mixed $value = null): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->where($column, $operator, $value);
	}

	public static function whereNot(string|array $column, mixed $operator = null, mixed $value = null): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereNot($column, $operator, $value);
	}

	public static function whereBetween(string $column, string|int $start, string|int $end): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereBetween($column, $start, $end);
	}

	public static function whereNotBetween(string $column, string|int $start, string|int $end): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereNotBetween($column, $start, $end);
	}

	public static function whereIn(string $column, Arrayable|array $options): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereIn($column, $options);
	}

	public static function whereNotIn(string $column, Arrayable|array $options): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereNotIn($column, $options);
	}

	public static function whereFullText(string|array $column, string $string): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereFullText($column, $string);
	}

	public static function whereNull(array|string $columns): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereNull($columns);
	}

	public static function whereNotNull(array|string $columns): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereNotNull($columns);
	}

	public static function whereLike(string $column, string $value, bool $wildcards = true): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereLike($column, $value, $wildcards);
	}

	public static function whereNotLike(string $column, string $value, bool $wildcards = true): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereNotLike($column, $value, $wildcards);
	}

	public static function whereRaw(string $condition, string|int $value): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereRaw($condition, $value);
	}

	// -----------------
	// Order

	public static function orderBy(string $column, string $order = 'ASC'): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->orderBy($column, $order);
	}

	public static function latest(string $column = 'created'): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->latest($column);
	}

	public static function oldest(string $column = 'created'): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->oldest($column);
	}

	// -----------------
	// Limits / Offsets / Pagination

	public static function limit(int $limit): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->limit($limit);
	}

	public static function offset(int $offset): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->offset($offset);
	}

	public static function page(int $number, int $size): Query
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->page($number, $size);
	}

	// -----------------
	// Chunks

	/**
	 * @throws Exception
	 */
	public static function chunk(int $amount, callable $callback): int
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->chunk($amount, $callback);
	}

	// -----------------
	// Select

	/**
	 * @throws Exception
	 */
	public static function count(): int
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->count();
	}

	/**
	 * @throws Exception
	 */
	public static function find(string|int|null $identifier, string|null $column = null, int $retention = 30): static|null
	{
		$model = new static;
		$column = $column ?? $model->getPrimaryKey();
//		$key = 'model_'.$model::class.':'.$column.':'.$identifier;

		// TODO: Fix model caching
//		return CacheManager::get()->remember($key, function () use ($model, $identifier, $column, $key) {
//			return $model->newQueryBuilder()->find($identifier, $column)?->saveCacheKey($key);
//		}, $retention);

		return $model->newQueryBuilder()->find($identifier, $column);
	}

	/**
	 * @throws Exception
	 */
	public static function all(): Bucket
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->get();
	}

	/**
	 * @throws Exception
	 */
	public static function allBy(string $column): Bucket
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->getBy($column);
	}

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
	// Insert

	/**
	 * @throws Exception
	 */
	public static function insert(array $attributes): int
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->insert($attributes);
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

	// -----------------

	protected function newQueryBuilder(): Query
	{
		$builder = new Query($this->connection);
		$builder->setTable($this->table);
		$builder->setModel(static::class);
		return $builder;
	}
}