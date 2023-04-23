<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support;

final class UrlTools
{

	protected function __construct()
	{
	}

	// -----------------

	public static function getPathUsingParams(string $path, array $params): string
	{
		if (empty($params) === false) {
			if (array_is_list($params)) {
				$path = preg_replace('/{(.*?)}/', '{parameter}', $path);
				$path = Str::replaceSequential($path, '{parameter}', $params);
			} else {
				foreach ($params as $key => $value) {
					$path = str_replace(sprintf('{%s}', $key), $value, $path);
				}
			}
		}
		return $path;
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