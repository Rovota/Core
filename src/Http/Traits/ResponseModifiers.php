<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http\Traits;

use JsonSerializable;
use Rovota\Core\Cookie\Cookie;
use Rovota\Core\Cookie\CookieManager;
use Rovota\Core\Http\Enums\StatusCode;
use Rovota\Core\Http\Response;
use Rovota\Core\Storage\Interfaces\FileInterface;
use Rovota\Core\Support\Str;
use Rovota\Core\Views\View;

trait ResponseModifiers
{
	private string|null $variant = null;
	private array|null $dimensions = null;

	// -----------------

	public function dimensions(int $width, int|null $height = null): Response
	{
		$this->dimensions = [$width, $height];
		return $this;
	}

	public function variant(string $name): Response
	{
		$this->variant = trim($name);
		return $this;
	}

	// -----------------

	public function requireAuth(string $scheme, array $options, StatusCode $code = StatusCode::Unauthorized): Response
	{
		$header = trim($scheme);
		foreach ($options as $name => $value) {
			$header .= sprintf(' %s="%s"', $name, $value);
		}

		$this->header('WWW-Authenticate', $header);
		$this->setHttpCode($code);
		return $this;
	}

	public function requireBasicAuth(string|null $realm = null, StatusCode $code = StatusCode::Unauthorized): Response
	{
		$this->requireAuth('Basic', $realm === null ? [] : ['realm' => $realm], $code);
		return $this;
	}

	public function clearSiteData(array $items = []): Response
	{
		$string = '"*"';
		if (empty($items) === false) {
			$string = ''; $counter = 0;
			foreach ($items as $item) {
				$string .= ($counter > 0) ? sprintf(', "%s"', $item) : sprintf('"%s"', $item);
				$counter++;
			}
		}
		$this->header('Clear-Site-Data', $string);
		return $this;
	}

}