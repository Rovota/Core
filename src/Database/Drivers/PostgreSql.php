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
use Rovota\Core\Database\Enums\Driver;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Support\Version;
use Throwable;

class PostgreSql extends Connection
{

	public function __construct(string $name, ConnectionConfig $config)
	{
		$parameters = $this->getMappedParameters($config);
		$dsn = $this->buildDsn($config->driver, $parameters);
		$connection = new PDO($dsn, $parameters['user'], $parameters['password'], $parameters['attributes']);

		parent::__construct($name, $connection, $config);
		$this->setEncoding($parameters['encoding']);
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

	// -----------------

	protected function buildDsn(Driver $driver, array $parameters): string
	{
		$host = $parameters['host'];
		$database = $parameters['database'];
		$port = $parameters['port'];

		$template = '%s:host=%s;dbname=%s';
		if ($port > 0) {
			$template .= ';port='.$port;
		}

		return sprintf($template, $driver->value, $host, $database);
	}

	protected function getMappedParameters(ConnectionConfig $config): array
	{
		return [
			'host' => $config->parameters->get('host', 'localhost'),
			'database' => $config->parameters->get('database', 'default'),
			'port' => $config->parameters->int('port', 5432),
			'user' => $config->parameters->get('user', 'admin'),
			'password' => $config->parameters->get('password'),
			'charset' => $config->parameters->get('charset', 'UTF8'),
			'attributes' => $config->parameters->array('attributes'),
		];
	}

	protected function setEncoding(string $encoding): bool
	{
		try {
			$this->query("SET CLIENT_ENCODING TO '?'", [$encoding]);
		} catch (Throwable) {
			ExceptionHandler::logMessage('notice', "The encoding '{encoding}' could not be synchronized. The encoding may not be supported.", ['encoding' => $encoding]);
			return false;
		}

		return true;
	}

}