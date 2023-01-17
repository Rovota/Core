<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Database\Builder;

use Rovota\Core\Database\Model;
use Rovota\Core\Support\Config;

/**
 * @property string|null $table
 * @property Model|null $model
 * @property int $include_deleted
 * @property bool $invalid
 */
final class QueryConfig extends Config
{

	protected function table(): string|null
	{
		return $this->get('table');
	}

	protected function model(): Model|null
	{
		return $this->get('model');
	}

	// -----------------

	protected function includeDeleted(): int
	{
		return $this->int('include_deleted');
	}

	// -----------------


	protected function invalid(): bool
	{
		return $this->bool('invalid');
	}

}