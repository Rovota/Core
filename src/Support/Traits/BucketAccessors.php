<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support\Traits;

trait BucketAccessors
{

	// -----------------

	public function only(string|array $keys, bool $allow_null = true): array
	{
		$result = [];
		foreach (is_array($keys) ? $keys : [$keys] as $key) {
			$input = $this->get($key);
			if ($input !== null || $allow_null) {
				$result[$key] = $this->get($key);
			}
		}
		return $result;
	}

	public function except(string|array $keys): array
	{
		$result = $this->all();
		foreach (is_array($keys) ? $keys : [$keys] as $key) {
			unset($result[$key]);
		}
		return $result;
	}

}