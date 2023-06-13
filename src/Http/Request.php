<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http;

use Rovota\Core\Facades\Cookie;
use Rovota\Core\Facades\Registry;
use Rovota\Core\Facades\Route;
use Rovota\Core\Http\Traits\RequestInput;
use Rovota\Core\Kernel\Application;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Routing\Route as RouteObject;
use Rovota\Core\Structures\ErrorBucket;
use Rovota\Core\Support\Arr;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Str;
use Rovota\Core\Support\Traits\Errors;
use Rovota\Core\Support\Traits\Macroable;
use Rovota\Core\Support\UrlTools;
use Rovota\Core\Validation\Enums\FilterAction;
use Rovota\Core\Validation\FilterManager;
use Rovota\Core\Validation\Traits\RequestValidation;
use Throwable;

final class Request
{
	use RequestInput, RequestValidation, Macroable, Errors;

	protected array $headers;

	protected array $client_names;
	protected array $client_robots;

	protected array|null $acceptable_content_types = null;
	protected array|null $acceptable_encodings = null;
	protected array|null $acceptable_locales = null;

	// -----------------

	public function __construct(string|null $body, array $post, array $query, array $headers)
	{
		$this->body = $body;
		$this->post = new RequestData($post);
		$this->query = new RequestData($query);
		$this->headers = $headers;

		$this->client_names = include base_path('vendor/rovota/core/src/Http/client_names.php');
		$this->client_robots = include base_path('vendor/rovota/core/src/Http/client_robots.php');

		$this->errors = new ErrorBucket();
	}

	public function __get(string $name): mixed
	{
		return $this->has($name) ? $this->get($name) : null;
	}

	// -----------------

	public function baseUrl(): string
	{
		$base = sprintf('%s://%s', $this->scheme(), Application::$server->get('server_name') ?? $this->targetHost());
		if ($this->port() === 80 || $this->port() === 443) {
			return $base;
		}
		return $base.':'.$this->port();
	}

	/**
	 * Uses the experimental 'Sec-CH-UA' HTTP header.
	 * For non-supported clients, it will guess the client name.
	 */
	public function client(string|null $default = null): string|null
	{
		$client = null;
		if ($this->hasHeader('Sec-CH-UA')) {
			$names = array_reduce(explode(',', trim($this->header('Sec-CH-UA'))),
				function ($carry, $element) {
					$brand = Str::remove(Str::beforeLast($element, ';'), '"');
					$version = str_contains($element, ';v=') ? Str::afterLast($element, ';v=') : '';
					if (Str::containsNone($brand, ['Brand', 'Chromium'])) {
						$carry[trim($brand)] = (int) Str::remove($version, '"');
					}
					return $carry;
				},[]
			);
			$client = array_key_first($names);
		}

		if ($client !== null) {
			return $client;
		}

		foreach ($this->client_names as $client => $name) {
			if (str_contains($this->header('User-Agent', ''), $client)) {
				return $name;
			}
		}
		return $default;
	}

	/**
	 * Requires either the Cloudflare IPCountry feature or the GeoIP PHP extension.
	 */
	public function country(): string|null
	{
		if ($this->hasHeader('CF-Connecting-IP')) {
			$country_code = $this->header('CF-IPCountry', 'XX');
			return $country_code === 'XX' || $country_code === 'T1' ? null : $country_code;
		}
		
		if (function_exists('geoip_country_code_by_name')) {
			$country_code = geoip_country_code_by_name($this->ip());
			return $country_code === false ? null : $country_code;
		}

		return null;
	}

	/**
	 * Uses the experimental 'Sec-CH-UA-Model' HTTP header.
	 * For non-supported clients, it will attempt to guess/approximate the device name.
	 */
	public function device(): string
	{
		if ($this->hasHeader('Sec-CH-UA-Model')) {
			return $this->header('Sec-CH-UA-Model');
		}

		$useragent = $this->header('User-Agent', '');
		$useragent = text($useragent)->remove(['(KHTML, like Gecko)', 'CPU '])->between('(', ')');

		if ($useragent->contains('iPad')) {
			return 'iPad';
		}
		if ($useragent->contains('iPhone')) {
			return 'iPhone';
		}
		if ($useragent->contains('CrOS')) {
			return 'Chromebook';
		}

		$device = text('');

		$parts = $useragent->explode(';');
		foreach ($parts as $part) {
			$part = trim($part);
			if (Str::containsAny($part, ['Linux', 'Android', 'Mobile', 'like Mac', 'Win64', 'x64', 'x86', 'Macintosh']) || mb_strlen($part) < 4) {
				continue;
			}
			$device->append(', '.$part);
		}

		$device->before('Build/');
		$device->before('rv:');
		$device->before('OS X');

		$device->replace(['NT 10.0', 'NT 6.3', 'NT 6.2', '_'], ['10', '8.1', '8.0', '.']);

		return trim(preg_replace('/[A-z]{2}-[A-z]{2}/', '', (string)$device), ', ');
	}

	public function fullUrl(): string
	{
		return $this->url().UrlTools::arrayToQuery($this->query->all());
	}

	public function fullUrlWithQuery(array|string $include): string
	{
		return $this->url().UrlTools::arrayToQuery($this->query->only($include)->all());
	}

	public function fullUrlWithoutQuery(array|string $exclude): string
	{
		return $this->url().UrlTools::arrayToQuery($this->query->except($exclude)->all());
	}

	public function getPassword(): string|null
	{
		$password = Application::$server->get('PHP_AUTH_PW');
		return Str::length($password) > 0 ? $password : null;
	}

	public function getUser(): string|null
	{
		$username = Application::$server->get('PHP_AUTH_USER');
		return Str::length($username) > 0 ? $username : null;
	}

	public function hasCredentials(): bool
	{
		return $this->getUser() !== null && $this->getPassword() !== null;
	}

	public function ip(): string
	{
		return match(true) {
			$this->hasHeader('CF-Connecting-IP') => $this->header('CF-Connecting-IP'),
			$this->hasHeader('X-Forwarded-For') => $this->header('X-Forwarded-For'),
			default => Application::$server->get('REMOTE_ADDR'),
		};
	}

	public function ipAllowed(string|null $ip = null): bool
	{
		$filter = FilterManager::get('access_control');
		if ($filter !== null && $filter->action === FilterAction::Block) {
			return Arr::contains($filter->values, $ip ?? $this->ip()) === false;
		}
		if ($filter !== null && $filter->action === FilterAction::Allow) {
			return Arr::contains($filter->values, $ip ?? $this->ip());
		}
		return true;
	}

	public function isBot(): bool
	{
		return text($this->header('User-Agent', ''))->lower()->containsAny($this->client_robots);
	}

	public function isJson(): bool
	{
		return text($this->header('Content-Type', ''))->lower()->contains('json');
	}

	public function isMethod(string $verb): bool
	{
		return $this->method() === strtoupper($verb);
	}

	public function isPost(): bool
	{
		return Application::$server->get('REQUEST_METHOD') === 'POST';
	}

	public function isProxy(): bool
	{
		$headers = ['Forwarded', 'X-Forwarded-For', 'Client-IP'];
		foreach ($headers as $header) {
			if ($header === 'X-Forwarded-For' && $this->hasHeader('CF-Connecting-IP')) {
				if (str_contains($this->header($header), ',')) {
					return true;
				}
				continue;
			}
			if ($this->hasHeader($header)) {
				return true;
			}
		}
		return false;
	}

	public function isSecure(): bool
	{
		return $this->scheme() === 'https' || Application::$server->get('HTTPS') === 'on';
	}

	public function isXmlHttp(): bool
	{
		return $this->header('X-Requested-With') === 'XMLHttpRequest';
	}

	public function method(): string
	{
		$method = Application::$server->get('REQUEST_METHOD');
		if ($method === 'POST' && $this->hasHeader('X-HTTP-Method-Override')) {
			$header_value = $this->header('X-HTTP-Method-Override');
			if (Arr::contains(['PUT', 'DELETE', 'PATCH'], $header_value)) {
				$method = $header_value;
			}
		}
		return $method;
	}
	
	public function path(): string
	{
		return Str::before(Application::$server->get('REQUEST_URI'), '?');
	}

	public function pathMatchesPattern(string $pattern): bool
	{
		$pattern = preg_replace('/\/{(.*?)}/', '/(.*?)', $pattern);
		return preg_match_all('#^' . $pattern . '$#', $this->path()) === 1;
	}

	/**
	 * Uses the experimental 'Sec-CH-UA-Platform' HTTP header.
	 * For non-supported clients, it will return 'Unknown'.
	 */
	public function platform(): string
	{
		$platform = $this->header('Sec-CH-UA-Platform');
		return $platform !== null ? trim($platform) : 'Unknown';
	}

	public function port(): int
	{
		return (int)Application::$server->get('SERVER_PORT');
	}

	public function protocol(): string
	{
		return Application::$server->get('SERVER_PROTOCOL');
	}

	public function queryString(): string
	{
		return UrlTools::arrayToQuery($this->query->all());
	}

	public function referrer(): string|null
	{
		return $this->header('Referer');
	}

	public function realMethod(): string
	{
		return Application::$server->get('REQUEST_METHOD');
	}

	public function remoteHost(): string
	{
		return Application::$server->get('REMOTE_HOST');
	}

	public function route(): RouteObject|null
	{
		return Route::current();
	}

	public function routeIsNamed(string $name): bool
	{
		if (str_ends_with($name, '*')) {
			return str_starts_with(Route::currentName() ?? '', str_replace('*', '', $name));
		}
		return Route::currentName() === $name;
	}

	public function scheme(): string
	{
		return Application::$server->get('REQUEST_SCHEME', 'https');
	}

	public function targetHost(bool $with_scheme = false): string
	{
		$host = Application::$server->get('HTTP_HOST');
		return $with_scheme ? sprintf('%s://%s', $this->scheme(), $host) : $host;
	}

	public function time(): Moment|null
	{
		try {
			return new Moment(Application::$server->get('REQUEST_TIME'));
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}

	public function url(): string
	{
		return sprintf('%s://%s%s', $this->scheme(), $this->targetHost(), $this->path());
	}

	public function useragent(): string|null
	{
		return $this->header('User-Agent');
	}

	// -----------------

	public function accepts(string|array $content_types): bool
	{
		$accepts = $this->getAcceptableContentTypes();
		if (empty($accepts)) {
			return true;
		}

		$types = is_array($content_types) ? $content_types : [$content_types];
		foreach ($accepts as $type => $value) {
			if (Arr::contains(['*/*', '*'], $type)) {
				return true;
			}
			foreach ($types as $name) {
				if ($this->matchesType($type, $name)) {
					return true;
				}
			}
		}

		return false;
	}

	public function acceptsAnyContentType(): bool
	{
		return $this->accepts('*/*');
	}

	public function acceptsHtml(): bool
	{
		return $this->accepts('text/html');
	}

	public function acceptsJson(): bool
	{
		return $this->accepts('application/json');
	}

	public function acceptsWebP(): bool
	{
		return $this->accepts('image/webp');
	}

	public function authToken(): string|null
	{
		$header = $this->header('Authorization');
		if ($header !== null) {
			$value = Str::after($header, ' ');
			return mb_strlen($value) > 0 ? $value : null;
		}
		return null;
	}

	public function authType(): string|null
	{
		$header = $this->header('Authorization');
		if ($header !== null) {
			$value = Str::before($header, ' ');
			return mb_strlen($value) > 0 ? $value : null;
		}
		return null;
	}

	public function bearerToken(): string|null
	{
		return $this->authType() === 'Bearer' ? $this->authToken() : null;
	}

	public function cacheControl(): string|null
	{
		return $this->header('Cache-Control');
	}

	public function cookie(string $name, string|null $default = null): string|null
	{
		$cookie = Cookie::findReceived($name);
		return $cookie !== null ? $cookie->value : $default;
	}

	public function encoding(): string|null
	{
		$accepts = $this->getAcceptableEncodings();
		return !empty($accepts) ? array_key_first($accepts) : null;
	}

	public function expects(): string|null
	{
		$accepts = $this->getAcceptableContentTypes();
		return !empty($accepts) ? array_key_first($accepts) : null;
	}

	public function format(): string
	{
		return $this->header('Content-Type');
	}

	public function hasCookie(string $name): bool
	{
		return Cookie::isReceived($name);
	}

	/**
	 * Only available when using Cloudflare.
	 */
	public function hasExposedCredentials(): bool
	{
		if ($this->hasheader('Exposed-Credential-Check')) {
			return (int)$this->header('Exposed-Credential-Check') === 1;
		} else return false;
	}

	public function hasHeader(string $name): bool
	{
		return isset($this->headers[Str::lower($name)]);
	}

	public function hasPrivacyControl(): bool
	{
		if ($this->hasheader('Sec-GPC')) {
			return (int)$this->header('Sec-GPC') === 1;
		} else return false;
	}

	public function header(string $name, string|null $default = null): string|null
	{
		return $this->headers[Str::lower($name)] ?? $default;
	}

	public function headers(): array
	{
		return $this->headers;
	}

	public function locale(): string
	{
		$accepts = $this->getAcceptableLocales();
		return array_key_first($accepts);
	}

	public function prefers(string|array $content_types): string|null
	{
		$accepts = $this->getAcceptableContentTypes();
		if (empty($accepts)) {
			return null;
		}

		$types = is_array($content_types) ? $content_types : [$content_types];
		foreach ($accepts as $accept => $value) {
			if (Arr::contains(['*/*', '*'], $accept)) {
				return $types[0];
			}

			foreach ($types as $type) {
				if ($this->matchesType($type, $accept) || $accept === strtok($type, '/').'/*') {
					return $type;
				}
			}
		}

		return null;
	}

	public function prefersEncoding(string|array $encodings): string|null
	{
		$accepts = $this->getAcceptableEncodings();
		if (empty($accepts)) {
			return null;
		}

		$encodings = is_array($encodings) ? $encodings : [$encodings];
		foreach ($accepts as $accept => $value) {
			if (Arr::contains($encodings, $accept)) {
				return $accept;
			}
		}
		return null;
	}

	public function prefersFresh(): bool
	{
		return $this->cacheControl() === 'no-cache';
	}

	public function prefersLocale(string|array $locales, string|null $default = null): string|null
	{
		$accepts = $this->getAcceptableLocales();
		if (empty($accepts)) {
			return $default;
		}

		$locales = is_array($locales) ? $locales : [$locales];
		foreach ($accepts as $accept => $value) {
			if (Arr::contains($locales, $accept)) {
				return $accept;
			}
		}
		return $default;
	}

	public function prefersSafeContent(): bool
	{
		return Str::containsAny($this->header('Prefer', ''), ['Safe', 'safe']);
	}

	// -----------------

	public function getAcceptableLocales(): array
	{
		if ($this->acceptable_locales !== null) {
			return $this->acceptable_locales;
		}

		$locales = Arr::fromAcceptHeader($this->header('Accept-Language'));
		if (empty($locales)) {
			return [Registry::string('default_locale', 'en_US') => 1.0];
		}

		$normalized = [];
		foreach ($locales as $locale => $quality) {
			$locale = mb_strlen($locale) === 2 ? $locale.'_'.strtoupper($locale) : $locale;
			$locale = str_replace('-', '_', $locale);
			if (!isset($locales[$locale])) {
				$normalized[$locale] = $quality;
			}
		}

		return $this->acceptable_locales = $normalized;
	}

	public function getAcceptableEncodings(): array
	{
		if ($this->acceptable_encodings !== null) {
			return $this->acceptable_encodings;
		}
		$encodings = Arr::fromAcceptHeader($this->header('Accept-Encoding'));
		return $this->acceptable_encodings = $encodings;
	}

	public function getAcceptableContentTypes(): array
	{
		if ($this->acceptable_content_types !== null) {
			return $this->acceptable_content_types;
		}
		$types = Arr::fromAcceptHeader($this->header('Accept'));
		return $this->acceptable_content_types = $types;
	}

	// -----------------

	protected function matchesType(string $actual, string $type): bool
	{
		if ($actual === $type) {
			return true;
		} else {
			$actual = explode('/', $actual);
			$type = explode('/', $type);
			if ($actual[0] !== $type[0]) {
				return false;
			}
			return $actual[1] === '*';
		}
	}

}