<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Interfaces;

use DateTimeZone;
use Envms\FluentPDO\Query as FluentQuery;
use PDO;
use PDOStatement;
use PHLAK\SemVer\Exceptions\InvalidVersionException;
use Rovota\Core\Database\Builder\Query;
use Rovota\Core\Database\ConnectionConfig;
use Rovota\Core\Support\Version;

interface ConnectionInterface
{

	public function isDefault(): bool;

	// -----------------

	public function name(): string;

	public function config(): ConnectionConfig;

	// -----------------

	/**
	 * @throws InvalidVersionException
	 */
	public function getVersion(): Version;

	// -----------------

	public function table(string $name): Query;

	public function getTables(): array;

	public function hasTable(string $name): bool;

	// -----------------

	public function setTimezone(DateTimeZone|string $timezone): bool;

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

	public function lastId(): string|int;

	// -----------------

	public function setAttribute(int $name, mixed $value): bool;

	public function getAttribute(int $name): mixed;

	// -----------------

	/**
	 * Only supported in combination with MySQL.
	 */
	public function getBufferState(): bool;

	/**
	 * Only supported in combination with MySQL.
	 */
	public function setBufferState(bool $state): void;

}