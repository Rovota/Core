<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Http\Client\Traits;

use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Support\Text;
use Throwable;

trait PasswordService
{
	/**
	 * Provide the SHA1 hash of the password you wish to check.
	 */
	public function hasPasswordMatches(string $hash): bool
	{
		return $this->countPasswordMatches($hash) > 0;
	}

	/**
	 * Provide the SHA1 hash of the password you wish to check.
	 */
	public function countPasswordMatches(string $hash): int
	{
		$hash = Text::upper($hash);
		$prefix = Text::limit($hash, 0, 5);
		$suffix = substr($hash, 5);

		$results = $this->retrievePasswordResults($prefix);
		if (array_key_exists($suffix, $results)) {
			return $results[$suffix];
		}

		return 0;
	}

	// -----------------

	protected function retrievePasswordResults(string $prefix): array
	{
		try {
			$response = $this->get('https://api.pwnedpasswords.com/range/'.$prefix)
				->header('Add-Padding', 'true')
				->connectTimeout(2)
				->execute()->string();
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
			return [];
		}

		$filtered_response = [];
		foreach (preg_split("/((\r?\n)|(\r\n?))/", $response) as $line) {
			[$suffix, $count] = explode(':', $line);
			if ((int)$count > 0) {
				$filtered_response[$suffix] = (int)$count;
			}
		}

		return $filtered_response;
	}

}