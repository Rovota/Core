<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Drivers;

use DateTimeZone;
use PDO;
use Rovota\Core\Database\Connection;
use Rovota\Core\Database\ConnectionConfig;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Support\Version;
use Throwable;

class PostgreSql extends Connection
{

	public function __construct(string $name, ConnectionConfig $config)
	{
		$parameters = [
			'host' => $config->parameters->get('host', 'localhost'),
			'database' => $config->parameters->get('database', 'default'),
			'port' => $config->parameters->int('port', 5432),
			'user' => $config->parameters->get('user', 'admin'),
			'password' => $config->parameters->get('password'),
			'charset' => $config->parameters->get('charset', 'utf8mb4'), // TODO: Check if Postgres uses this as well.
			'attributes' => $config->parameters->array('attributes'),
		];

		$dsn = $this->buildDsn($config->driver, $parameters);
		$connection = new PDO($dsn, $parameters['user'], $parameters['password'], $parameters['attributes']);

		parent::__construct($name, $connection, $config);
	}

	// -----------------

	public function getVersion(): Version
	{
		$result = $this->query('SELECT VERSION() as version')->fetch();
		return new Version($result->version);
	}

	// -----------------

	public function getTables(): array
	{
		return $this->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
	}

	// -----------------

	public function setTimezone(DateTimeZone|string $timezone): bool
	{
		$timezone =  is_string($timezone) ? $timezone : $timezone->getName();

		try {
			$this->query("SET time_zone = '?'", [$timezone]);
		} catch (Throwable) {
			ExceptionHandler::logMessage('notice', "The timezone '{timezone}' could not be synchronized. The SQL timezone database may be missing.", ['timezone' => $timezone]);
			return false;
		}

		return true;
	}

	public function hasTimezoneData(): bool
	{
		$result = $this->query("SELECT CONVERT_TZ('2000-01-01 1:00:00','UTC','Europe/Amsterdam') AS time")->fetch();
		return (bool)$result->time;
	}

	// -----------------

	public function getBufferState(): bool
	{
		return true;
	}

	public function setBufferState(bool $state): void
	{
	}

}