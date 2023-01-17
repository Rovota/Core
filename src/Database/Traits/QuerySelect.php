<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Traits;

use Envms\FluentPDO\Exception;
use Rovota\Core\Database\Model;
use Rovota\Core\Structures\Bucket;
use stdClass;

trait QuerySelect
{

	// -----------------
	// COUNT

	/**
	 * @throws Exception
	 */
	public function count(): int
	{
		$this->setQueryMode('select');

		if ($this->config->include_deleted === 0) {
			$this->whereNull('deleted');
		}
		if ($this->config->include_deleted === 2) {
			$this->whereNotNull('deleted');
		}

		$this->buildQuery();

		$buffer_state = $this->connection->getBufferState();
		$this->connection->setBufferState(true);
		$count = $this->fluent->count();
		$this->connection->setBufferState($buffer_state);

		return $count;
	}


	// -----------------
	// SINGLE

	/**
	 * @throws Exception
	 */
	public function find(string|int|null $identifier, string $column = 'id'): Model|stdClass|null
	{
		$this->where($column, $identifier ?? '');
		$this->setQueryMode('select');
		return $this->fetchAll()[0] ?? null;
	}

	/**
	 * @throws Exception
	 */
	public function first(): Model|stdClass|null
	{
		$this->limit(1);
		$this->setQueryMode('select');
		return $this->fetchAll()[0] ?? null;
	}

	// -----------------
	// MULTIPLE

	/**
	 * @throws Exception
	 */
	public function get(): Bucket
	{
		$this->setQueryMode('select');
		return new Bucket($this->fetchAll());
	}

	/**
	 * @throws Exception
	 */
	public function getBy(string $column): Bucket
	{
		$this->setQueryMode('select');
		return new Bucket($this->fetchAll($column));
	}

}