<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Http\Resources;

use JsonSerializable;
use Rovota\Core\Database\Model;

/**
 * @internal
 */
abstract class Resource implements JsonSerializable
{

	// TODO: Finish the Resource functionality.

	private Model|null $model;

	// -----------------

	public function __construct(Model|null $model)
	{
		$this->model = $model;
	}

	public function __get(string $name): mixed
	{
		return $this->model->attribute($name);
	}

	public function __toString(): string
	{
		return $this->toJson();
	}

	// -----------------

	public function toJson(): string
	{
		return json_encode_clean($this->jsonSerialize());
	}

	public function toArray(): array
	{
		return [];
	}

	public function jsonSerialize(): array
	{
		return $this->toArray();
	}

}