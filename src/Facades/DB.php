<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Facades;

use Envms\FluentPDO\Query as FluentQuery;
use PDO;
use PDOStatement;
use Rovota\Core\Database\Builder\Query;
use Rovota\Core\Database\ConnectionManager;
use Rovota\Core\Database\Interfaces\ConnectionInterface;
use Rovota\Core\Support\Str;

final class DB
{

	protected function __construct()
	{
	}

	// -----------------

	public static function connection(string|null $name = null): ConnectionInterface
	{
		return ConnectionManager::get($name);
	}

	public static function build(array $config, string|null $name = null): ConnectionInterface|null
	{
		return ConnectionManager::build($name ?? Str::random(20), $config);
	}

	// -----------------

	public static function table(string $name): Query
	{
		return ConnectionManager::get()->table($name);
	}

	public static function hasTable(string $name): bool
	{
		return ConnectionManager::get()->hasTable($name);
	}

	// -----------------

	public static function select(string $query, array $params = []): array
	{
		return ConnectionManager::get()->select($query, $params);
	}

	public static function insert(string $query, array $params = []): int
	{
		return ConnectionManager::get()->insert($query, $params);
	}

	public static function update(string $query, array $params = []): int
	{
		return ConnectionManager::get()->update($query, $params);
	}

	public static function delete(string $query, array $params = []): int
	{
		return ConnectionManager::get()->delete($query, $params);
	}

	public static function query(string $query, array $params = []): PDOStatement
	{
		return ConnectionManager::get()->query($query, $params);
	}

	public static function prepare(string $query): PDOStatement
	{
		return ConnectionManager::get()->prepare($query);
	}

	public static function execute(PDOStatement $statement, array $params): PDOStatement
	{
		return ConnectionManager::get()->execute($statement, $params);
	}

	// -----------------

	public static function beginTransaction(): bool
	{
		return ConnectionManager::get()->beginTransaction();
	}

	public static function inTransaction(): bool
	{
		return ConnectionManager::get()->inTransaction();
	}

	public static function commit(): bool
	{
		return ConnectionManager::get()->commit();
	}

	public static function rollBack(): bool
	{
		return ConnectionManager::get()->rollBack();
	}

	// -----------------

	public static function raw(): PDO
	{
		return ConnectionManager::get()->raw();
	}

	public static function fluent(): FluentQuery
	{
		return ConnectionManager::get()->fluent();
	}

	public static function lastId(): string|int
	{
		return ConnectionManager::get()->lastId();
	}

}