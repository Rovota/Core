<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support;

use Rovota\Core\Structures\Bucket;

final class UrlTools
{

	protected function __construct()
	{
	}

	// -----------------

	public static function queryToArray(string $url, bool $decode = true): array
	{
		$query = [];
		$segments = explode('&', $url);
		
		foreach ($segments as $segment) {
			if (str_contains($segment, '=')) {
				$item = explode('=', $segment);
				$query[$item[0]] = trim($decode ? rawurlencode($item[1]) : $item[1]);
			} else {
				$query[$segment] = null;
			}
		}
		
		return $query;
	}

	public static function arrayToQuery(array $fields = [], bool $encode = true): string
	{
		$items = '';
		foreach ($fields as $key => $value) {
			$value = (string)$value;
			if (Str::length($value) > 0) {
				$value = $encode ? rawurlencode($value) : $value;
				$items .= sprintf('%s%s=%s', (Str::length($items) > 0) ? '&' : '', $key, $value);
			}
		}
		return (Str::length($items) > 0) ? '?'.$items : '';
	}

}