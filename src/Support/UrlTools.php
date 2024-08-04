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

}