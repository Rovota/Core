<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Database\Interfaces;

use Envms\FluentPDO\Query;
use PDO;
use PDOStatement;
use Rovota\Core\Database\QueryBuilder;
use Rovota\Core\Support\Version;

interface ConnectionInterface
{

	public function isDefault(): bool;

	// -----------------

	public function name(): string;

	// -----------------

	public function option(string $name): string|int|array|null;

	public function driver(): string;

	public function label(): string;

	public function host(): string;

	public function database(): string;

	public function port(): int;

	public function user(): string;

	// -----------------

	public function version(): Version;

	// -----------------

	public function table(string $name): QueryBuilder;

	// -----------------

	public function hasTable(string $name): bool;

	public function hasTimezoneData(): bool;

	// -----------------

	public function select(string $query, array $params = []): array;

	public function insert(string $query, array $params = []): int;

	public function update(string $query, array $params = []): int;

	public function delete(string $query, array $params = []): int;

	public function query(string $query, array $params = []): PDOStatement;

	public function prepare(string $query): PDOStatement;

	public function execute(PDOStatement $statement, array $params): PDOStatement;

	// -----------------

	public function beginTransaction(): bool;

	public function inTransaction(): bool;

	public function commit(): bool;

	public function rollBack(): bool;

	// -----------------

	public function raw(): PDO;

	public function fluent(): Query;

	public function lastId(): string|int;

	// -----------------

	/**
	 * Only supported with MySQL.
	 */
	public function getBufferState(): bool;

	/**
	 * Only supported with MySQL.
	 */
	public function setBufferState(bool $state): void;

}