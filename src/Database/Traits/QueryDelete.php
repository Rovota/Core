<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Traits;

use Envms\FluentPDO\Exception;

trait QueryDelete
{

	/**
	 * @throws Exception
	 */
	public function delete(bool $permanent = false): int
	{
		return $permanent ? $this->forceDelete() : $this->softDelete();
	}

	/**
	 * @throws Exception
	 */
	public function forceDelete(): int
	{
		$this->setQueryMode('delete');
		$this->buildQuery();

		return $this->connection->delete($this->fluent->getQuery(), $this->fluent->getParameters());
	}

	/**
	 * @throws Exception
	 */
	public function softDelete(): int
	{
		return $this->update('deleted', now());
	}

}