<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Routing;

use Rovota\Core\Routing\Enums\Scheme;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Support\Str;
use Rovota\Core\Support\UrlTools;

final class UrlObject
{

	public string|null $raw = null;

	public Scheme $scheme = Scheme::Https;
	public string|null $subdomain = null;
	public string|null $domain = null;
	public int|null $port = null;
	public string|null $path = null;
	public array $query = [];

	// -----------------

	public function __construct(Bucket $data = new Bucket([]))
	{
		foreach ($data as $key => $value) {
			if (property_exists($this, $key)) {
				$this->{$key} = $value;
			}
		}
	}

	public function __toString(): string
	{
		return $this->build();
	}

	// -----------------

	public static function from(string $url): UrlObject
	{
		$data = self::getDataFromUrl(trim($url));
		return new UrlObject($data);
	}

	public static function fromArray(array $data): UrlObject
	{
		$data = new Bucket($data);
		return new UrlObject($data);
	}

	// -----------------

	protected static function getDataFromUrl(string $url): Bucket
	{
		$data = new Bucket([
			'raw' => $url,
		]);

		$scheme = text($url)->before('://')->toString();
		$data->set('scheme', Scheme::tryFrom($scheme) ?? Scheme::Https);
		$url = Str::remove($url, $scheme.'://');

		if (str_contains($url, '?')) {
			$query = Str::after($url, '?');
			$data->set('query', UrlTools::queryToArray($query));
			$url = Str::remove($url, '?'.$query);
		}

		if (str_contains($url, '/')) {
			$path = Str::after($url, '/');
			$data->set('path', $path);
			$url = Str::remove($url, '/'.$path);
		}

		if (str_contains($url, ':')) {
			$port = Str::after($url, ':');
			$data->set('port', (int)$port);
			$url = Str::remove($url, ':'.$port);
		}

		if (Str::occurrences($url, '.') > 1) {
			$subdomain = Str::before($url, '.');
			$data->set('subdomain', $subdomain);
			$url = Str::remove($url, $subdomain.'.');
		}

		$data->set('domain', $url);

		return $data;
	}

	protected function build(bool $relative = false): string
	{
		$scheme = $this->scheme->value.'://';
		$subdomain = is_string($this->subdomain) ? $this->subdomain.'.' : '';
		$port = $this->port !== 80 && $this->port !== 443 ? (is_int($this->port) ? ':'.$this->port : '') : '';

		$path = is_string($this->path) ? '/'.$this->path : '/';
		$query = UrlTools::arrayToQuery($this->query);

		$absolute = $scheme.$subdomain.$this->domain.$port;
		return trim(($relative === false ? $absolute : '').$path.$query, '/');
	}

}