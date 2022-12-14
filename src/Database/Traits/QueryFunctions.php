<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Traits;

use Rovota\Core\Cache\CacheManager;
use Rovota\Core\Database\Model;
use Rovota\Core\Database\QueryBuilder;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Interfaces\Arrayable;

trait QueryFunctions
{

	// -----------------
	// WHERE

	public static function where(string|array $column, mixed $operator = null, mixed $value = null): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->where($column, $operator, $value);
	}

	public static function whereNot(string|array $column, mixed $operator = null, mixed $value = null): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereNot($column, $operator, $value);
	}

	public static function whereBetween(string $column, string|int $start, string|int $end): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereBetween($column, $start, $end);
	}

	public static function whereNotBetween(string $column, string|int $start, string|int $end): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereNotBetween($column, $start, $end);
	}

	public static function whereIn(string $column, Arrayable|array $options): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereIn($column, $options);
	}

	public static function whereNotIn(string $column, Arrayable|array $options): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereNotIn($column, $options);
	}

	public static function whereFullText(string|array $column, string $string): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereFullText($column, $string);
	}

	public static function whereNull(array|string $columns): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereNull($columns);
	}

	public static function whereNotNull(array|string $columns): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereNotNull($columns);
	}

	public static function whereLike(string $column, string $value): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereLike($column, $value);
	}

	public static function whereNotLike(string $column, string $value): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereNotLike($column, $value);
	}

	public static function whereRaw(string $condition, string|int $value): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->whereRaw($condition, $value);
	}

	// -----------------
	// ORDER

	public static function orderBy(string $column, string $order = 'ASC'): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->orderBy($column, $order);
	}

	public static function latest(string $column = 'created'): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->latest($column);
	}

	public static function oldest(string $column = 'created'): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->oldest($column);
	}

	// -----------------
	// LIMITS / OFFSETS

	public static function limit(int $limit): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->limit($limit);
	}

	public static function offset(int $offset): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->offset($offset);
	}

	// -----------------
	// CHUNKS

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public static function chunk(int $amount, callable $callback): int
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->chunk($amount, $callback);
	}

	// -----------------
	// SELECT

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public static function count(): int
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->count();
	}

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public static function find(string|int|null $identifier, string|null $column = null, int $retention = 30): static|null
	{
		$model = new static();
		$column = $column ?? $model->getPrimaryKey();
		$key = 'model_'.$model::class.':'.$column.':'.$identifier;
		
		return CacheManager::get()->remember($key, function () use ($model, $identifier, $column, $key) {
			return $model->newQueryBuilder()->find($identifier, $column)?->saveCacheKey($key);
		}, $retention);
	}

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public static function all(): Bucket
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->get();
	}

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public static function allBy(string $column): Bucket
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->getBy($column);
	}

	public static function withDeleted(): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->withDeleted();
	}

	public static function onlyDeleted(): QueryBuilder
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->onlyDeleted();
	}

	// -----------------
	// INSERT

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public static function insert(array $attributes): int
	{
		$builder = (new static)->newQueryBuilder();
		return $builder->insert($attributes);
	}

	// -----------------
	// Delete

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public static function destroy(array|string|int $identifiers, string|null $column = null, bool $permanent = false): int
	{
		$model = new static();
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

	protected function newQueryBuilder(): QueryBuilder
	{
		$builder = new QueryBuilder($this->table, $this->connection);
		return $builder->setModel(static::class);
	}
}