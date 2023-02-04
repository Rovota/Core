<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Builder;

use Envms\FluentPDO\Exception;
use Envms\FluentPDO\Queries\Base;
use Envms\FluentPDO\Query as FluentQuery;
use Rovota\Core\Database\CastManager;
use Rovota\Core\Database\ConnectionManager;
use Rovota\Core\Database\Interfaces\ConnectionInterface;
use Rovota\Core\Database\Model;
use Rovota\Core\Database\Traits\QueryConstraints;
use Rovota\Core\Database\Traits\QueryDelete;
use Rovota\Core\Database\Traits\QueryMisc;
use Rovota\Core\Database\Traits\QuerySelect;
use Rovota\Core\Database\Traits\QueryUpdate;
use Rovota\Core\Support\Traits\Conditionable;

final class Query
{
	use Conditionable, QueryConstraints, QueryDelete, QueryMisc, QuerySelect, QueryUpdate;

	protected ConnectionInterface $connection;

	protected QueryConfig $config;

	protected array $statements = [];

	protected FluentQuery|Base $fluent;

	// protected bool $cache_enabled = true;
	// protected bool $cache_retention = 30;

	// -----------------

	public function __construct(ConnectionInterface|string $connection, QueryConfig|null $config = null)
	{
		$this->connection = is_string($connection) ? ConnectionManager::get($connection) : $connection;
		$this->config = $config ?? new QueryConfig();
		$this->fluent = $this->connection->fluent();
	}

	// -----------------
	// Setup

	public function setTable(string $table): Query
	{
		$this->config->table = trim($table);
		return $this;
	}

	public function setModel(Model|string $model): Query
	{
		$this->config->model = is_string($model) ? new $model : $model;
		return $this;
	}

	public function setConfig(QueryConfig $config): Query
	{
		$this->config = $config;
		return $this;
	}

	public function setFluent(FluentQuery|Base $fluent): Query
	{
		$this->fluent = $fluent;
		return $this;
	}

	// -----------------

	/**
	 * @throws Exception
	 */
	protected function fetchAll(string $column = ''): array
	{
		if ($this->config->include_deleted === 0) {
			$this->whereNull('deleted');
		}
		if ($this->config->include_deleted === 2) {
			$this->whereNotNull('deleted');
		}

		$this->buildQuery();

		// TODO: Cache the fetched result for the set retention period, with the query result as key, provided caching is enabled.
		// Make sure that if caching is disabled, models load their data without cache as well.
		// make sure to think about the save/delete etc functionality, removing the cached items.
		// e.g. create a spot to store the key used, which can then be immediately used by the model to set that key in its own storage.
		// This cache key needs to be set for newly cached models as well as those stored in cache.

		if ($this->config->model === null) {
			$result = $this->fluent->fetchAll($column);
		} else {
			$objects = $this->fluent->fetchAll($column);
			if (is_array($objects) === false) {
				return [];
			}

			$result = [];
			foreach ($objects as $key => $object) {
				$result[$key] = $this->config->model::newFromBuilder($object);
			}
		}

		return is_array($result ?? null) ? $result : [];
	}

	// -----------------
	// Misc

	protected function buildQuery(): void
	{
		foreach ($this->statements as $statement) {
			$function = $statement['type'];
			$arguments = $statement['args'];
			$this->fluent->$function(...$arguments);
		}
	}

	/**
	 * @throws Exception
	 */
	protected function setQueryMode(string $mode): void
	{
		$this->fluent = match ($mode) {
			'select' => $this->fluent->from($this->config->table),
			'insert' => $this->fluent->insertInto($this->config->table),
			'update' => $this->fluent->update($this->config->table),
			'delete' => $this->fluent->deleteFrom($this->config->table),
			default => $this->fluent
		};
	}

	protected function addStatement(string $type, ...$arguments): void
	{
		$this->statements[] = ['type' => $type, 'args' => $arguments];
	}

	protected function normalized(string $attribute, mixed $value): mixed
	{
		if ($this->config->model !== null) {
			if ($this->config->model->hasCast($attribute) && $value !== null) {
				return CastManager::castToRaw($value, $this->config->model->getCast($attribute));
			}
		}
		return CastManager::castToRawAutomatic($value);
	}

}