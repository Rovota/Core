<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Traits;

use Envms\FluentPDO\Exception;

trait QueryUpdate
{

	// -----------------
	// UPDATE

	/**
	 * @throws Exception
	 */
	public function update(array|string $column, mixed $value = null): int
	{
		if (is_array($column)) {
			foreach ($column as $key => $value) {
				$column[$key] = $this->normalized($key, $value);
			}
			$value = null;
		} else {
			$value = $this->normalized($column, $value);
		}

		$this->setQueryMode('update');
		$this->addStatement('set', $column, $value);
		$this->buildQuery();

		return $this->connection->update($this->fluent->getQuery(), $this->fluent->getParameters());
	}

	// -----------------
	// RECOVER

	/**
	 * @throws Exception
	 */
	public function recover(): int
	{
		return $this->update('deleted');
	}

}