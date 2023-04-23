<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database;

use Envms\FluentPDO\Query as FluentQuery;
use PDO;
use PDOStatement;
use Rovota\Core\Database\Builder\Query;
use Rovota\Core\Database\Interfaces\ConnectionInterface;
use Rovota\Core\Support\Traits\Conditionable;

abstract class Connection implements ConnectionInterface
{
	use Conditionable;

	protected string $name;

	protected ConnectionConfig $config;

	protected PDO $connection;

	// -----------------

	protected array $tables;

	// protected array $schema;

	// -----------------

	public function __construct(string $name, PDO $connection, ConnectionConfig $config)
	{
		$this->name = $name;
		$this->config = $config;

		$this->connection = $connection;
		$this->tables = $this->getTables();

		$this->setTimezone('UTC');
		$this->setDefaultAttributes();
		$this->setCustomAttributes();
	}

	// -----------------

	public function __toString(): string
	{
		return $this->name;
	}

	public function __get(string $name): mixed
	{
		return $this->config->get($name);
	}

	public function __isset(string $name): bool
	{
		return $this->config->has($name);
	}

	// -----------------

	public function isDefault(): bool
	{
		return ConnectionManager::getDefault() === $this->name;
	}

	// -----------------

	public function name(): string
	{
		return $this->name;
	}

	public function config(): ConnectionConfig
	{
		return $this->config;
	}

	// -----------------

	public function table(string $name): Query
	{
		$query = new Query($this);
		return $query->setTable($name);
	}

	public function hasTable(string $name): bool
	{
		return in_array($name, $this->tables, true);
	}

	// -----------------

	public function select(string $query, array $params = []): array
	{
		return $this->query($query, $params)->fetchAll();
	}

	public function insert(string $query, array $params = []): int
	{
		return $this->query($query, $params)->rowCount();
	}

	public function update(string $query, array $params = []): int
	{
		return $this->query($query, $params)->rowCount();
	}

	public function delete(string $query, array $params = []): int
	{
		return $this->query($query, $params)->rowCount();
	}

	public function query(string $query, array $params = []): PDOStatement
	{
		if (count($params) === 0) {
			return $this->connection->query($query);
		} else {
			$statement = $this->connection->prepare($query);
			$statement->execute($params);
			return $statement;
		}
	}

	public function prepare(string $query): PDOStatement
	{
		return $this->connection->prepare($query);
	}

	public function execute(PDOStatement $statement, array $params): PDOStatement
	{
		$statement->execute($params);
		return $statement;
	}

	// -----------------

	public function beginTransaction(): bool
	{
		return $this->connection->beginTransaction();
	}

	public function inTransaction(): bool
	{
		return $this->connection->inTransaction();
	}

	public function commit(): bool
	{
		return $this->connection->commit();
	}

	public function rollBack(): bool
	{
		return $this->connection->rollBack();
	}

	// -----------------

	public function raw(): PDO
	{
		return $this->connection;
	}

	public function fluent(): FluentQuery
	{
		return new FluentQuery($this->raw());
	}

	public function lastId(): string|int
	{
		$id = $this->connection->lastInsertId();
		return is_numeric($id) ? (int)$id : $id;
	}

	// -----------------

	public function setAttribute(int $name, mixed $value): bool
	{
		return $this->connection->setAttribute($name, $value);
	}

	public function getAttribute(int $name): mixed
	{
		return $this->connection->getAttribute($name);
	}

	// -----------------

	protected function setDefaultAttributes(): void
	{
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
		$this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
	}

	protected function setCustomAttributes(): void
	{
		foreach ($this->config->parameters->get('attributes') as $name => $value) {
			$this->setAttribute($name, $value);
		}
	}

}