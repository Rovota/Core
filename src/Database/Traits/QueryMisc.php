<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Traits;

use Envms\FluentPDO\Exception;

trait QueryMisc
{

	// -----------------
	// GROUPING

	public function groupBy(string $column): static
	{
		$this->addStatement('groupBy', $column);
		return $this;
	}

	// -----------------
	// ORDER

	public function orderBy(string $column, string $order = 'ASC'): static
	{
		$this->addStatement('orderBy', $column.' '.strtoupper($order));
		return $this;
	}

	public function latest(string $column = 'created'): static
	{
		$this->addStatement('orderBy', sprintf('%s %s', $column, 'DESC'));
		return $this;
	}

	public function oldest(string $column = 'created'): static
	{
		$this->addStatement('orderBy', sprintf('%s %s', $column, 'ASC'));
		return $this;
	}

	// -----------------
	// LIMITS / OFFSETS

	public function limit(int $limit): static
	{
		$this->addStatement('limit', $limit);
		return $this;
	}

	public function offset(int $offset): static
	{
		$this->addStatement('offset', $offset);
		return $this;
	}

	// -----------------
	// CHUNKS

	/**
	 * @throws Exception
	 */
	public function chunk(int $amount, callable $callback): int
	{
		$offset = 0;
		$continue = true;
		$counter = 0;

		while ($continue === true) {
			$clone = clone $this;
			$results = $clone->offset($offset)->limit($amount)->get();

			if ($results->isEmpty()) break;
			$callback($results);
			if ($results->count() < $amount) break;

			$results = null;
			$offset = $offset + $amount;
			$counter++;
		}

		return $counter;
	}

	// -----------------
	// INSERT

	/**
	 * @throws Exception
	 */
	public function insert(array $data): int
	{
		if (array_is_list($data)) {
			$counter = 0;
			foreach ($data as $values) {
				$counter = $counter + $this->insert($values);
			}
			return $counter;
		}

		foreach ($data as $key => $value) {
			$data[$key] = $this->normalized($key, $value);
		}

		$this->setQueryMode('insert');
		$this->addStatement('values', $data);
		$this->buildQuery();

		return $this->connection->insert($this->fluent->getQuery(), $this->fluent->getParameters());
	}

	// -----------------
	// CACHING

	// public function withoutCache(): QueryBuilder
	// {
	// 	$this->cache_enabled = false;
	// 	return $this;
	// }

	// public function cacheRetention(int $retention): QueryBuilder
	// {
	// 	$this->cache_retention = $retention;
	// 	return $this;
	// }

}