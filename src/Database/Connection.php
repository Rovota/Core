<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database;

use Envms\FluentPDO\Query;
use PDO;
use PDOStatement;
use Rovota\Core\Database\Interfaces\ConnectionInterface;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Support\Version;
use Throwable;

final class Connection implements ConnectionInterface
{

	protected string $name;

	protected PDO $connection;

	protected array $options = [
		'pdo' => [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false
		]
	];

	protected array $tables;

	// -----------------

	public function __construct(string $name, array $options)
	{
		$this->name = $name;

		foreach ($options as $key => $value) {
			if ($key === 'options') {
				foreach ($value as $option => $param) {
					$this->options['pdo'][$option] = $param;
				}
			} else {
				$this->options[$key] = $value;
			}
		}

		$this->connection = new PDO($this->buildDsn(), $this->user(), $this->options['password'], $this->options['pdo']);
		$this->tables = $this->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

		try {
			$this->query("SET time_zone = 'UTC'");
		} catch (Throwable) {
			ExceptionHandler::logMessage('notice', "The timezone '{timezone}' could not be synchronized. The SQL timezone database may be missing.", ['timezone' => 'UTC']);
		}

		$this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
	}

	// -----------------

	public function __get(string $name): mixed
	{
		return $this->options[$name] ?? null;
	}

	public function __isset(string $name): bool
	{
		return isset($this->options[$name]);
	}

	// -----------------

	public function isDefault(): bool
	{
		return DatabaseManager::getDefault() === $this->name;
	}

	// -----------------

	public function name(): string
	{
		return $this->name;
	}

	// -----------------

	public function option(string $name): string|int|array
	{
		return $this->options[$name];
	}

	public function driver(): string
	{
		return $this->option('driver');
	}

	public function label(): string
	{
		return $this->option('label');
	}

	public function host(): string
	{
		return $this->option('host');
	}

	public function database(): string
	{
		return $this->option('database');
	}

	public function port(): int
	{
		return $this->option('port');
	}

	public function user(): string
	{
		return $this->option('user');
	}

	// -----------------

	/**
	 * @throws \PHLAK\SemVer\Exceptions\InvalidVersionException
	 */
	public function version(): Version
	{
		$result = $this->query("SELECT VERSION() as version")->fetch();
		return new Version($result->version);
	}

	// -----------------

	public function table(string $name): QueryBuilder
	{
		return new QueryBuilder($name, $this->name);
	}

	// -----------------

	public function hasTable(string $name): bool
	{
		return in_array($name, $this->tables, true);
	}

	public function hasTimezoneData(): bool
	{
		$result = $this->query("SELECT CONVERT_TZ('2023-01-01 1:00:00','UTC','Europe/Amsterdam') AS time")->fetch();
		return (bool)$result->time;
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

	public function fluent(): Query
	{
		return new Query($this->raw());
	}

	public function lastId(): string|int
	{
		$id = $this->connection->lastInsertId();
		return is_numeric($id) ? (int)$id : $id;
	}

	// -----------------

	public function getBufferState(): bool
	{
		if ($this->driver() === 'mysql') {
			return $this->raw()->getAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY);
		} else {
			return false;
		}
	}

	public function setBufferState(bool $state): void
	{
		if ($this->driver() === 'mysql') {
			$this->raw()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $state);
		}
	}

	// -----------------

	protected function buildDsn(): string
	{
		$dsn = sprintf('%s:host=%s;dbname=%s;charset=%s', $this->driver(), $this->host(), $this->database(), $this->options['charset']);
		if ($this->port() > 0) {
			$dsn .= ';port='.$this->port();
		}
		return $dsn;
	}

}