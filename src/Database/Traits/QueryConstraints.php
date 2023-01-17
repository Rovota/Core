<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Traits;

use Rovota\Core\Support\Interfaces\Arrayable;
use Rovota\Core\Support\Str;

trait QueryConstraints
{

	// -----------------
	// WHERE

	public function where(string|array $column, mixed $operator = null, mixed $value = null): static
	{
		if (is_array($column)) {
			foreach ($column as $col => $values) {
				$this->where($col, $values);
			}
			return $this;
		}

		if ($value === null) {
			$value = $operator;
			$operator = '=';
		}

		$column = Str::before($column, ' ');
		$this->addStatement('where', sprintf('%s %s ?', $column, $operator), self::normalized($column, $value));

		return $this;
	}

	public function whereNot(string|array $column, mixed $operator = null, mixed $value = null): static
	{
		if (is_array($column)) {
			foreach ($column as $col => $values) {
				$this->whereNot($col, $values);
			}
			return $this;
		}

		if ($value === null) {
			$value = $operator;
			$operator = '=';
		}

		$column = Str::before($column, ' ');
		$this->addStatement('where', sprintf('NOT %s %s ?', $column, $operator), self::normalized($column, $value));

		return $this;
	}

	public function whereBetween(string $column, string|int $start, string|int $end): static
	{
		$this->addStatement('where', $column.' BETWEEN ? AND ?', [self::normalized($column, $start), self::normalized($column, $end)]);
		return $this;
	}

	public function whereNotBetween(string $column, string|int $start, string|int $end): static
	{
		$this->addStatement('where', $column.' NOT BETWEEN ? AND ?', [self::normalized($column, $start), self::normalized($column, $end)]);
		return $this;
	}

	public function whereIn(string $column, Arrayable|array $options): static
	{
		$options = $options instanceof Arrayable ? $options->toArray() : $options;
		foreach ($options as $key => $value) {
			$options[$key] = self::normalized($column, $value);
		}
		$this->addStatement('where', $column, $options);
		return $this;
	}

	public function whereNotIn(string $column, Arrayable|array $options): static
	{
		$options = $options instanceof Arrayable ? $options->toArray() : $options;
		foreach ($options as $key => $value) {
			$options[$key] = self::normalized($column, $value);
		}
		$this->addStatement('where', $column.' NOT', $options);
		return $this;
	}

	public function whereFullText(string|array $column, string $string): static
	{
		if (is_array($column)) {
			$column = implode(', ', $column);
		}
		$this->addStatement('where', sprintf('MATCH(%s) AGAINST(? IN NATURAL LANGUAGE MODE)', $column), $string);
		return $this;
	}

	public function whereNull(array|string $columns): static
	{
		if (is_array($columns)) {
			foreach ($columns as $column) {
				$this->whereNull($column);
			}
			return $this;
		}
		$this->addStatement('where', $columns.' IS NULL');
		return $this;
	}

	public function whereNotNull(array|string $columns): static
	{
		if (is_array($columns)) {
			foreach ($columns as $column) {
				$this->whereNotNull($column);
			}
			return $this;
		}
		$this->addStatement('where', $columns.' IS NOT NULL');
		return $this;
	}

	public function whereLike(string $column, string $value): static
	{
		$this->addStatement('where', $column." LIKE '?'", self::normalized($column, $value));
		return $this;
	}

	public function whereNotLike(string $column, string $value): static
	{
		$this->addStatement('where', $column." NOT LIKE '?'", self::normalized($column, $value));
		return $this;
	}

	public function whereRaw(string $condition, string|int $value): static
	{
		$this->addStatement('where', $condition, $value);
		return $this;
	}


	// -----------------
	// OR WHERE

	public function orWhere(string|array $column, mixed $operator = null, mixed $value = null): static
	{
		if (is_array($column)) {
			foreach ($column as $col => $values) {
				$this->orWhere($col, $values);
			}
			return $this;
		}

		if ($value === null) {
			$value = $operator;
			$operator = '=';
		}

		$column = Str::before($column, ' ');
		$this->addStatement('whereOr', sprintf('%s %s ?', $column, $operator), self::normalized($column, $value));

		return $this;
	}

	public function orWhereBetween(string $column, string|int $start, string|int $end): static
	{
		$this->addStatement('whereOr', $column.' BETWEEN ? AND ?', [self::normalized($column, $start), self::normalized($column, $end)]);
		return $this;
	}

	public function orWhereNotBetween(string $column, string|int $start, string|int $end): static
	{
		$this->addStatement('whereOr', $column.' NOT BETWEEN ? AND ?', [self::normalized($column, $start), self::normalized($column, $end)]);
		return $this;
	}

	public function orWhereIn(string $column, Arrayable|array $options): static
	{
		$options = $options instanceof Arrayable ? $options->toArray() : $options;
		foreach ($options as $key => $value) {
			$options[$key] = self::normalized($column, $value);
		}
		$this->addStatement('whereOr', $column, $options);
		return $this;
	}

	public function orWhereNotIn(string $column, Arrayable|array $options): static
	{
		$options = $options instanceof Arrayable ? $options->toArray() : $options;
		foreach ($options as $key => $value) {
			$options[$key] = self::normalized($column, $value);
		}
		$this->addStatement('whereOr', $column.' NOT', $options);
		return $this;
	}

	public function orWhereFullText(string|array $column, string $string): static
	{
		if (is_array($column)) {
			$column = implode(', ', $column);
		}
		$this->addStatement('whereOr', sprintf('MATCH(%s) AGAINST(? IN NATURAL LANGUAGE MODE)', $column), $string);
		return $this;
	}

	public function orWhereNull(string $column): static
	{
		$this->addStatement('whereOr', $column.' IS NULL');
		return $this;
	}

	public function orWhereNotNull(string $column): static
	{
		$this->addStatement('whereOr', $column.' IS NOT NULL');
		return $this;
	}

	public function orWhereLike(string $column, string $value): static
	{
		$this->addStatement('whereOr', $column." LIKE '?'", self::normalized($column, $value));
		return $this;
	}

	public function orWhereNotLike(string $column, string $value): static
	{
		$this->addStatement('whereOr', $column." NOT LIKE '?'", self::normalized($column, $value));
		return $this;
	}

	public function orWhereRaw(string $condition, string|int $value): static
	{
		$this->addStatement('whereOr', $condition, $value);
		return $this;
	}


	// -----------------
	// HAVING

	public function having(string|array $column, mixed $operator, mixed $value = null): static
	{
		if (is_array($column)) {
			foreach ($column as $col => $values) {
				$this->having($col, $values);
			}
			return $this;
		}

		if ($value === null) {
			$value = $operator;
			$operator = '=';
		}

		$column = Str::before($column, ' ');
		$this->addStatement('having', sprintf('%s %s ?', $column, $operator), $value);

		return $this;
	}

	public function havingBetween(string $column, string|int $start, string|int $end): static
	{
		$this->addStatement('having', sprintf('%s BETWEEN %s AND %s', $column, self::normalized($column, $start), self::normalized($column, $end)));
		return $this;
	}

	// -----------------
	// DELETED STATUS

	public function withDeleted(): static
	{
		$this->config->include_deleted = 1;
		return $this;
	}

	public function onlyDeleted(): static
	{
		$this->config->include_deleted = 2;
		return $this;
	}

}