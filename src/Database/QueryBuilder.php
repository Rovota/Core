<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Database;

use Envms\FluentPDO\Queries\Base;
use Envms\FluentPDO\Queries\Select;
use Envms\FluentPDO\Query;
use Rovota\Core\Database\Interfaces\ConnectionInterface;
use Rovota\Core\Support\Collection;
use Rovota\Core\Support\Text;
use Rovota\Core\Support\Traits\Conditionable;
use stdClass;

final class QueryBuilder
{
	use Conditionable;

	protected string $table;
	protected ConnectionInterface $connection;

	protected Model|string|null $model = null;

	protected Query|Base|Select $query;
	protected array $statements = [];
	protected int $include_deleted = 0;

	// protected bool $cache_enabled = true;
	// protected bool $cache_retention = 30;

	protected bool $invalid = false;

	// -----------------

	public function __construct(string $table, string $connection)
	{
		$connection = DatabaseManager::get($connection);

		$this->query = $connection->fluent();
		$this->connection = $connection;
		$this->table = $table;
	}

	// -----------------
	// WHERE

	public function where(string|array $column, mixed $operator = null, mixed $value = null): QueryBuilder
	{
		if (is_array($column)) {
			foreach ($column as $col => $values) {
				$this->where($col, $values);
			}
			return $this;
		}

		if ($value === null) {
			$value = $operator;
			$operator = '=';
		}

		$column = Text::before($column, ' ');
		$this->addStatement('where', sprintf('%s %s ?', $column, $operator), self::normalized($column, $value));

		return $this;
	}

	public function whereNot(string|array $column, mixed $operator = null, mixed $value = null): QueryBuilder
	{
		if (is_array($column)) {
			foreach ($column as $col => $values) {
				$this->whereNot($col, $values);
			}
			return $this;
		}

		if ($value === null) {
			$value = $operator;
			$operator = '=';
		}

		$column = Text::before($column, ' ');
		$this->addStatement('where', sprintf('NOT %s %s ?', $column, $operator), self::normalized($column, $value));

		return $this;
	}

	public function whereBetween(string $column, string|int $start, string|int $end): QueryBuilder
	{
		$this->addStatement('where', $column.' BETWEEN ? AND ?', [self::normalized($column, $start), self::normalized($column, $end)]);
		return $this;
	}

	public function whereNotBetween(string $column, string|int $start, string|int $end): QueryBuilder
	{
		$this->addStatement('where', $column.' NOT BETWEEN ? AND ?', [self::normalized($column, $start), self::normalized($column, $end)]);
		return $this;
	}

	public function whereIn(string $column, Collection|array $options): QueryBuilder
	{
		$options = $options instanceof Collection ? $options->all() : $options;
		foreach ($options as $key => $value) {
			$options[$key] = self::normalized($column, $value);
		}
		$this->addStatement('where', $column, $options);
		return $this;
	}

	public function whereNotIn(string $column, Collection|array $options): QueryBuilder
	{
		$options = $options instanceof Collection ? $options->all() : $options;
		foreach ($options as $key => $value) {
			$options[$key] = self::normalized($column, $value);
		}
		$this->addStatement('where', $column.' NOT', $options);
		return $this;
	}

	public function whereFullText(string|array $column, string $string): QueryBuilder
	{
		if (is_array($column)) {
			$column = implode(', ', $column);
		}
		$this->addStatement('where', sprintf('MATCH(%s) AGAINST(? IN NATURAL LANGUAGE MODE)', $column), $string);
		return $this;
	}

	public function whereNull(array|string $columns): QueryBuilder
	{
		if (is_array($columns)) {
			foreach ($columns as $column) {
				$this->whereNull($column);
			}
			return $this;
		}
		$this->addStatement('where', $columns.' IS NULL');
		return $this;
	}

	public function whereNotNull(array|string $columns): QueryBuilder
	{
		if (is_array($columns)) {
			foreach ($columns as $column) {
				$this->whereNotNull($column);
			}
			return $this;
		}
		$this->addStatement('where', $columns.' IS NOT NULL');
		return $this;
	}

	public function whereLike(string $column, string $value): QueryBuilder
	{
		$this->addStatement('where', $column." LIKE '?'", self::normalized($column, $value));
		return $this;
	}

	public function whereNotLike(string $column, string $value): QueryBuilder
	{
		$this->addStatement('where', $column." NOT LIKE '?'", self::normalized($column, $value));
		return $this;
	}

	public function whereRaw(string $condition, string|int $value): QueryBuilder
	{
		$this->addStatement('where', $condition, $value);
		return $this;
	}

	// -----------------
	// OR WHERE

	public function orWhere(string|array $column, mixed $operator = null, mixed $value = null): QueryBuilder
	{
		if (is_array($column)) {
			foreach ($column as $col => $values) {
				$this->orWhere($col, $values);
			}
			return $this;
		}

		if ($value === null) {
			$value = $operator;
			$operator = '=';
		}

		$column = Text::before($column, ' ');
		$this->addStatement('whereOr', sprintf('%s %s ?', $column, $operator), self::normalized($column, $value));

		return $this;
	}

	public function orWhereBetween(string $column, string|int $start, string|int $end): QueryBuilder
	{
		$this->addStatement('whereOr', $column.' BETWEEN ? AND ?', [self::normalized($column, $start), self::normalized($column, $end)]);
		return $this;
	}

	public function orWhereNotBetween(string $column, string|int $start, string|int $end): QueryBuilder
	{
		$this->addStatement('whereOr', $column.' NOT BETWEEN ? AND ?', [self::normalized($column, $start), self::normalized($column, $end)]);
		return $this;
	}

	public function orWhereIn(string $column, Collection|array $options): QueryBuilder
	{
		$options = $options instanceof Collection ? $options->all() : $options;
		foreach ($options as $key => $value) {
			$options[$key] = self::normalized($column, $value);
		}
		$this->addStatement('whereOr', $column, $options);
		return $this;
	}

	public function orWhereNotIn(string $column, Collection|array $options): QueryBuilder
	{
		$options = $options instanceof Collection ? $options->all() : $options;
		foreach ($options as $key => $value) {
			$options[$key] = self::normalized($column, $value);
		}
		$this->addStatement('whereOr', $column.' NOT', $options);
		return $this;
	}

	public function orWhereFullText(string|array $column, string $string): QueryBuilder
	{
		if (is_array($column)) {
			$column = implode(', ', $column);
		}
		$this->addStatement('whereOr', sprintf('MATCH(%s) AGAINST(? IN NATURAL LANGUAGE MODE)', $column), $string);
		return $this;
	}

	public function orWhereNull(string $column): QueryBuilder
	{
		$this->addStatement('whereOr', $column.' IS NULL');
		return $this;
	}

	public function orWhereNotNull(string $column): QueryBuilder
	{
		$this->addStatement('whereOr', $column.' IS NOT NULL');
		return $this;
	}

	public function orWhereLike(string $column, string $value): QueryBuilder
	{
		$this->addStatement('whereOr', $column." LIKE '?'", self::normalized($column, $value));
		return $this;
	}

	public function orWhereNotLike(string $column, string $value): QueryBuilder
	{
		$this->addStatement('whereOr', $column." NOT LIKE '?'", self::normalized($column, $value));
		return $this;
	}

	public function orWhereRaw(string $condition, string|int $value): QueryBuilder
	{
		$this->addStatement('whereOr', $condition, $value);
		return $this;
	}

	// -----------------
	// GROUPING / HAVING

	public function groupBy(string $column): QueryBuilder
	{
		$this->addStatement('groupBy', $column);
		return $this;
	}

	public function having(string|array $column, mixed $operator, mixed $value = null): QueryBuilder
	{
		if (is_array($column)) {
			foreach ($column as $col => $values) {
				$this->having($col, $values);
			}
			return $this;
		}

		if ($value === null) {
			$value = $operator;
			$operator = '=';
		}

		$column = Text::before($column, ' ');
		$this->addStatement('having', sprintf('%s %s ?', $column, $operator), $value);

		return $this;
	}

	public function havingBetween(string $column, string|int $start, string|int $end): QueryBuilder
	{
		$this->addStatement('having', sprintf('%s BETWEEN %s AND %s', $column, self::normalized($column, $start), self::normalized($column, $end)));
		return $this;
	}

	// -----------------
	// ORDER

	public function orderBy(string $column, string $order = 'ASC'): QueryBuilder
	{
		$this->addStatement('orderBy', $column.' '.strtoupper($order));
		return $this;
	}

	public function latest(string $column = 'created'): QueryBuilder
	{
		$this->addStatement('orderBy', sprintf('%s %s', $column, 'DESC'));
		return $this;
	}

	public function oldest(string $column = 'created'): QueryBuilder
	{
		$this->addStatement('orderBy', sprintf('%s %s', $column, 'ASC'));
		return $this;
	}

	// -----------------
	// LIMITS / OFFSETS

	public function limit(int $limit): QueryBuilder
	{
		$this->addStatement('limit', $limit);
		return $this;
	}

	public function offset(int $offset): QueryBuilder
	{
		$this->addStatement('offset', $offset);
		return $this;
	}

	// -----------------
	// CHUNKS

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public function chunk(int $amount, callable $callback): int
	{
		$offset = 0;
		$continue = true;
		$counter = 0;

		while ($continue === true) {
			$clone = clone $this;
			$results = $clone->offset($offset)->limit($amount)->get();

			if ($results->isEmpty()) break;
			$callback($results);
			if ($results->count() < $amount) break;

			$results = null;
			$offset = $offset + $amount;
			$counter++;
		}

		return $counter;
	}

	// -----------------
	// SELECT

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public function count(): int
	{
		$this->setQueryMode('select');

		if ($this->include_deleted === 0) {
			$this->whereNull('deleted');
		}
		if ($this->include_deleted === 2) {
			$this->whereNotNull('deleted');
		}

		$this->buildQuery();

		$buffer_state = $this->connection->getBufferState();
		$this->connection->setBufferState(true);
		$count = $this->query->count();
		$this->connection->setBufferState($buffer_state);

		return $count;
	}

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public function find(string|int|null $identifier, string $column = 'id'): Model|stdClass|null
	{
		$this->where($column, $identifier ?? '');
		$this->setQueryMode('select');
		return $this->fetchAll()[0] ?? null;
	}

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public function first(): Model|stdClass|null
	{
		$this->limit(1);
		$this->setQueryMode('select');
		return $this->fetchAll()[0] ?? null;
	}

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public function get(): Collection
	{
		$this->setQueryMode('select');
		return new Collection($this->fetchAll());
	}

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public function getBy(string $column): Collection
	{
		$this->setQueryMode('select');
		return new Collection($this->fetchAll($column));
	}

	public function withDeleted(): QueryBuilder
	{
		$this->include_deleted = 1;
		return $this;
	}

	public function onlyDeleted(): QueryBuilder
	{
		$this->include_deleted = 2;
		return $this;
	}

	// public function withoutCache(): QueryBuilder
	// {
	// 	$this->cache_enabled = false;
	// 	return $this;
	// }

	// public function cacheRetention(int $retention): QueryBuilder
	// {
	// 	$this->cache_retention = $retention;
	// 	return $this;
	// }

	// -----------------
	// INSERT

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public function insert(array $data): int
	{
		if (array_is_list($data)) {
			$counter = 0;
			foreach ($data as $values) {
				$counter = $counter + $this->insert($values);
			}
			return $counter;
		}

		foreach ($data as $key => $value) {
			$data[$key] = $this->normalized($key, $value);
		}

		$this->setQueryMode('insert');
		$this->addStatement('values', $data);
		$this->buildQuery();

		return $this->connection->insert($this->query->getQuery(), $this->query->getParameters());
	}

	// -----------------
	// UPDATE

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public function update(array|string $column, mixed $value = null): int
	{
		if (is_array($column)) {
			foreach ($column as $key => $value) {
				$column[$key] = $this->normalized($key, $value);
			}
			$value = null;
		} else {
			$value = $this->normalized($column, $value);
		}

		$this->setQueryMode('update');
		$this->addStatement('set', $column, $value);
		$this->buildQuery();

		return $this->connection->update($this->query->getQuery(), $this->query->getParameters());
	}

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public function recover(): int
	{
		return $this->update('deleted');
	}

	// -----------------
	// DELETE

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public function delete(bool $permanent = false): int
	{
		return $permanent ? $this->forceDelete() : $this->softDelete();
	}

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public function forceDelete(): int
	{
		$this->setQueryMode('delete');
		$this->buildQuery();

		return $this->connection->delete($this->query->getQuery(), $this->query->getParameters());
	}

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public function softDelete(): int
	{
		return $this->update('deleted', now());
	}

	// -----------------
	// Helpers

	public function setModel(string $model): QueryBuilder
	{
		$this->model = new $model();
		return $this;
	}

	// -----------------

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	protected function fetchAll(string $column = ''): array
	{
		if ($this->include_deleted === 0) {
			$this->whereNull('deleted');
		}
		if ($this->include_deleted === 2) {
			$this->whereNotNull('deleted');
		}

		$this->buildQuery();

		// TODO: Cache the fetched result for the set retention period, with the query result as key, provided caching is enabled.
		// Make sure that if caching is disabled, models load their data without cache as well.
		// make sure to think about the save/delete etc functionality, removing the cached items.
		// e.g. create a spot to store the key used, which can then be immediately used by the model to set that key in its own storage.
		// This cache key needs to be set for newly cached models as well as those stored in cache.

		if ($this->model === null) {
			$result = $this->query->fetchAll($column);
		} else {
			$objects = $this->query->fetchAll($column);
			if (is_array($objects) === false) {
				return [];
			}

			$result = [];
			foreach ($objects as $key => $object) {
				$result[$key] = $this->model::newFromBuilder($object);
			}
		}

		return is_array($result ?? null) ? $result : [];
	}

	// -----------------

	// public function setStatements(array $statements): QueryBuilder
	// {
	// 	$this->statements = $statements;
	// }

	// -----------------

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	protected function setQueryMode(string $mode): void
	{
		$this->query = match ($mode) {
			'select' => $this->query->from($this->table),
			'insert' => $this->query->insertInto($this->table),
			'update' => $this->query->update($this->table),
			'delete' => $this->query->deleteFrom($this->table),
			default => $this->query
		};
	}

	protected function buildQuery(): void
	{
		foreach ($this->statements as $statement) {
			$function = $statement['type'];
			$arguments = $statement['args'];
			$this->query->$function(...$arguments);
		}
	}

	protected function addStatement(string $type, ...$arguments): void
	{
		$this->statements[] = ['type' => $type, 'args' => $arguments];
	}

	protected function normalized(string $attribute, mixed $value): mixed
	{
		if ($this->model !== null) {
			if ($this->model->hasCast($attribute) && $value !== null) {
				return CastManager::castToRaw($value, $this->model->getCast($attribute));
			}
		}
		return CastManager::castToRawAutomatic($value);
	}

}