<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Envms\FluentPDO\Query;
use PDO;
use PDOStatement;
use Rovota\Core\Database\Connection;
use Rovota\Core\Database\DatabaseManager;
use Rovota\Core\Database\QueryBuilder;
use Rovota\Core\Support\Version;

final class DB
{

	protected function __construct()
	{
	}

	// -----------------

	public static function connection(string|null $name = null): Connection
	{
		return DatabaseManager::get($name);
	}

	// -----------------

	public static function version(): Version
	{
		return DatabaseManager::get()->version();
	}

	// -----------------

	public static function table(string $name): QueryBuilder
	{
		return DatabaseManager::get()->table($name);
	}

	// -----------------

	public static function hasTable(string $name): bool
	{
		return DatabaseManager::get()->hasTable($name);
	}

	public static function hasTimezoneData(): bool
	{
		return DatabaseManager::get()->hasTimezoneData();
	}

	// -----------------

	public static function select(string $query, array $params = []): array
	{
		return DatabaseManager::get()->select($query, $params);
	}

	public static function insert(string $query, array $params = []): int
	{
		return DatabaseManager::get()->insert($query, $params);
	}

	public static function update(string $query, array $params = []): int
	{
		return DatabaseManager::get()->update($query, $params);
	}

	public static function delete(string $query, array $params = []): int
	{
		return DatabaseManager::get()->delete($query, $params);
	}

	public static function query(string $query, array $params = []): PDOStatement
	{
		return DatabaseManager::get()->query($query, $params);
	}

	public static function prepare(string $query): PDOStatement
	{
		return DatabaseManager::get()->prepare($query);
	}

	public static function execute(PDOStatement $statement, array $params): PDOStatement
	{
		return DatabaseManager::get()->execute($statement, $params);
	}

	// -----------------

	public static function beginTransaction(): bool
	{
		return DatabaseManager::get()->beginTransaction();
	}

	public static function inTransaction(): bool
	{
		return DatabaseManager::get()->inTransaction();
	}

	public static function commit(): bool
	{
		return DatabaseManager::get()->commit();
	}

	public static function rollBack(): bool
	{
		return DatabaseManager::get()->rollBack();
	}

	// -----------------

	public static function raw(): PDO
	{
		return DatabaseManager::get()->raw();
	}

	public static function fluent(): Query
	{
		return DatabaseManager::get()->fluent();
	}

	public static function lastId(): string|int
	{
		return DatabaseManager::get()->lastId();
	}

}