<?php

namespace Rovota\Core\Support\Structures;

class Map extends Collection
{

	public function get(mixed $key): mixed
	{
		return $this->offsetGet($key);
	}

	// -----------------

	public function keys(): Sequence
	{
		return new Sequence(array_values($this->keys));
	}

	public function values(): Sequence
	{
		return new Sequence(array_values($this->values));
	}

}