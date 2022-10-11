<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Http\Traits;

use JsonSerializable;
use Rovota\Core\Cookie\Cookie;
use Rovota\Core\Cookie\CookieManager;
use Rovota\Core\Http\Enums\StatusCode;
use Rovota\Core\Http\Response;
use Rovota\Core\Storage\File;
use Rovota\Core\Support\Text;
use Rovota\Core\Views\View;

trait ResponseModifiers
{

	protected array $headers;
	private string|null $variant = null;
	private array|null $dimensions = null;
	protected StatusCode $status_code;

	// -----------------

	public function header(string $name, string $value): Response
	{
		if (Text::length($name) > 0 && Text::length($value) > 0) {
			$this->headers[$name] = $value;
		}
		return $this;
	}

	public function headers(array $headers): Response
	{
		foreach ($headers as $name => $value) {
			$this->headers[$name] = $value;
		}
		return $this;
	}

	public function withoutHeader(string $name): Response
	{
		unset($this->headers[$name]);
		return $this;
	}

	public function withoutHeaders(array $names = []): Response
	{
		if (empty($names)) {
			$this->headers = [];
		} else {
			foreach ($names as $name) {
				unset($this->headers[$name]);
			}
		}
		return $this;
	}

	// -----------------

	public function cookie(Cookie|string $name, string|null $value = null, array $options = []): Response
	{
		CookieManager::queue($name, $value, $options);
		return $this;
	}
	
	public function withoutCookie(string $name): Response
	{
		CookieManager::removeQueued($name);
		return $this;
	}

	public function expireCookie(string $name): Response
	{
		CookieManager::expire($name);
		return $this;
	}

	// -----------------

	/**
	 * When no extension is specified, one will be determined based on the content provided.
	 */
	public function download(string $name = null): Response
	{
		if (str_contains($name, '.') === false) {
			$name = match(true) {
				$this->content instanceof File => sprintf('%s.%s', $name, $this->content->extension),
				$this->content instanceof View => sprintf('%s.%s', $name, 'html'),
				$this->content instanceof JsonSerializable, is_array($this->content) => sprintf('%s.%s', $name, 'json'),
				default => sprintf('%s.%s', $name, 'txt'),
			};
		}
		$this->header('Content-Disposition', ($name === null) ? 'attachment;' : sprintf('attachment; filename="%s"', $name));
		return $this;
	}

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

	public function setContentType(string $value): Response
	{
		$this->header('Content-Type', trim($value));
		return $this;
	}

	public function setHttpCode(StatusCode $code): Response
	{
		$this->status_code = $code;
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