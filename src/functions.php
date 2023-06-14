<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

use Dflydev\DotAccessData\Data;
use League\Flysystem\FilesystemException;
use Rovota\Core\Auth\AccessManager;
use Rovota\Core\Auth\ApiToken;
use Rovota\Core\Auth\AuthManager;
use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Auth\Interfaces\SessionAuthentication;
use Rovota\Core\Auth\Interfaces\TokenAuthentication;
use Rovota\Core\Auth\User;
use Rovota\Core\Cache\CacheManager;
use Rovota\Core\Convert\ConversionManager;
use Rovota\Core\Cookie\Cookie;
use Rovota\Core\Cookie\CookieManager;
use Rovota\Core\Http\ApiError;
use Rovota\Core\Http\ApiErrorResponse;
use Rovota\Core\Http\Enums\StatusCode;
use Rovota\Core\Http\RedirectResponse;
use Rovota\Core\Http\Request;
use Rovota\Core\Http\RequestManager;
use Rovota\Core\Http\Response;
use Rovota\Core\Http\ResponseManager;
use Rovota\Core\Kernel\Application;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Kernel\Registry;
use Rovota\Core\Partials\Partial;
use Rovota\Core\Partials\PartialManager;
use Rovota\Core\Routing\UrlBuilder;
use Rovota\Core\Session\SessionManager;
use Rovota\Core\Storage\Interfaces\FileInterface;
use Rovota\Core\Storage\StorageManager;
use Rovota\Core\Structures\Bucket;
use Rovota\Core\Structures\Map;
use Rovota\Core\Structures\Sequence;
use Rovota\Core\Structures\Set;
use Rovota\Core\Support\Arr;
use Rovota\Core\Support\Text;
use Rovota\Core\Support\Interfaces\Arrayable;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Str;
use Rovota\Core\Support\ValidationTools;
use Rovota\Core\Views\Exceptions\MissingViewException;
use Rovota\Core\Views\View;
use Rovota\Core\Views\ViewManager;

// -----------------
// Strings

if (!function_exists('text')) {
	function text(string $string): Text
	{
		return new Text($string);
	}
}

if (!function_exists('string')) {
	/**
	 * @deprecated
	 */
   function string(string $string): Text
   {
      return new Text($string);
   }
}

if (!function_exists('__')) {
   function __(string|null $string, array|object $args = [], string|null $source = null): string
   {
      return Str::translate($string, $args, $source);
   }
}

if (!function_exists('e')) {
   function e(string|null $string): string|null
   {
      return Str::escape($string);
   }
}

if (!function_exists('convert_to_html')) {
   function convert_to_html(string $string, string|null $language = null): string
   {
      return ConversionManager::toHtml($string, $language);
   }
}

if (!function_exists('convert_to_ascii')) {
	function convert_to_ascii(string $string): string
	{
		return ConversionManager::toAscii($string);
	}
}

// -----------------
// DateTime

if (!function_exists('now')) {
	function now(DateTimeZone|null $timezone = null): Moment|null
	{
		try {
			return new Moment(timezone: $timezone);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}
}

if (!function_exists('moment')) {
	function moment(mixed $datetime = 'now', DateTimeZone|string|null $timezone = null): Moment|null
	{
		try {
			return Moment::create($datetime, $timezone);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
		}
		return null;
	}
}

// -----------------
// Sanitization Helpers

if (!function_exists('sanitize_select')) {
	function sanitize_select(string $option, array $options, string $fallback): string
	{
		return Arr::contains($options, $option) ? $option : $fallback;
	}
}

if (!function_exists('sanitize_extension')) {
	function sanitize_extension(string $type, string $extension): string|null
	{
		$extensions = ValidationTools::mimeTypeExtensions($type);

		if (Arr::contains($extensions, $extension)) {
			return $extension;
		}

		return $extensions[0] ?? null;
	}
}

if (!function_exists('sanitize_mime_type')) {
	function sanitize_mime_type(string $extension, string $type): string|null
	{
		$mime_types = ValidationTools::extensionMimeTypes($extension);

		if (Arr::contains($mime_types, $type)) {
			return $type;
		}

		return $mime_types[0] ?? null;
	}
}

// -----------------
// Components

if (!function_exists('cache')) {
	function cache(string|int|array|null $key = null): mixed
	{
		if ($key === null) {
			return CacheManager::get();
		}
		return CacheManager::get()->get($key);
	}
}

if (!function_exists('session')) {
	function session(array|string|null $key = null): mixed
	{
		if ($key === null) {
			return SessionManager::get();
		}
		if (is_string($key)) {
			return SessionManager::get()->read($key);
		}
		SessionManager::get()->putMany($key);
		return true;
	}
}

if (!function_exists('registry')) {
	function registry(string|null $name = null, mixed $default = null): Registry|string|bool|int|float|array|null
	{
		if ($name === null) {
			return Application::$registry;
		}

		return match(true) {
			is_string($default) => Application::$registry->string($name, $default),
			is_bool($default) => Application::$registry->bool($name, $default),
			is_int($default) => Application::$registry->int($name, $default),
			is_float($default) => Application::$registry->float($name, $default),
			is_array($default) => Application::$registry->array($name, $default),
			default => Application::$registry->get($name)?->value,
		};
	}
}

if (!function_exists('cookie')) {
	function cookie(string $name, string|null $value, array $options = []): Cookie
	{
		return CookieManager::make($name, $value, $options);
	}
}

if (!function_exists('request')) {
	function request(): Request
	{
		return RequestManager::getRequest();
	}
}

if (!function_exists('response')) {
	function response(mixed $content, StatusCode $code = StatusCode::Ok): Response
	{
		return ResponseManager::make($content, $code);
	}
}

if (!function_exists('api_error')) {
	function api_error(Throwable|ApiError|array $error, StatusCode $code = StatusCode::BadRequest): ApiErrorResponse
	{
		return ResponseManager::apiError($error, $code);
	}
}

if (!function_exists('redirect')) {
	function redirect(string|null $path = null, array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		return ResponseManager::redirect($path, $query, $code);
	}
}

if (!function_exists('to_route')) {
	function to_route(string $name, array $params = [], array $query = [], StatusCode $code = StatusCode::Found): RedirectResponse
	{
		return ResponseManager::redirect(null, [], $code)->route($name, $params, $query, $code);
	}
}

if (!function_exists('view')) {
	/**
	 * @throws MissingViewException
	 */
	function view(string $name, string|null $source = null): View
	{
		return ViewManager::make($name, $source);
	}
}

if (!function_exists('partial')) {
	function partial(string $name, string|null $source = null, array $variables = []): Partial|string
	{
		try {
			return PartialManager::make($name, $source, $variables);
		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
			return '';
		}
	}
}

if (!function_exists('file')) {
	/**
	 * @throws FilesystemException
	 */
	function file(string $location, string|null $disk = null): FileInterface|null
	{
		return StorageManager::get($disk)->file($location);
	}
}

if (!function_exists('asset')) {
	function asset(string $path, array $query = [], string|null $disk = null): UrlBuilder|null
	{
		if ($disk === null && StorageManager::isConnected('public')) {
			$disk = 'public';
		}

		$disk = StorageManager::get($disk);
		if ($disk !== null) {
			$path = $disk->root().Str::start($path, '/');
			return url()->domain($disk->domain())->path($path)->query($query);
		}

		return null;
	}
}

if (!function_exists('identity')) {
	function identity(): Identity|null
	{
		return AuthManager::activeProvider()?->identity();
	}
}

if (!function_exists('user')) {
	function user(): User|null
	{
		return AuthManager::activeProvider()?->user();
	}
}

if (!function_exists('token')) {
	/**
	 * Should only be used in combination with a Provider using the TokenAuthentication interface. Will return null if there's no authenticated token.
	 */
	function token(): ApiToken|null
	{
		$provider = AuthManager::activeProvider();
		return $provider instanceof TokenAuthentication ? $provider->getToken() : null;
	}
}

// -----------------
// Structures

if (!function_exists('as_bucket')) {
	function as_bucket(mixed $items = []): Bucket
	{
		return new Bucket($items);
	}
}

if (!function_exists('as_map')) {
	function as_map(mixed $items = []): Map
	{
		return new Map($items);
	}
}

if (!function_exists('as_sequence')) {
	function as_sequence(mixed $items = []): Sequence
	{
		return new Sequence($items);
	}
}

if (!function_exists('as_set')) {
	function as_set(mixed $items = []): Set
	{
		return new Set($items);
	}
}

// -----------------
// Misc

if (!function_exists('quit')) {
   function quit(StatusCode $code = StatusCode::InternalServerError): never
   {
      Application::quit($code);
   }
}

if (!function_exists('dump')) {
   function dump(mixed $data, bool $exit = false): void
   {
      print_r($data);
      if ($exit) quit();
   }
}

if (!function_exists('debug')) {
	function debug(Throwable|string $throwable, string|null $message = '', bool $unhandled = false): void
	{
		$throwable = is_string($throwable) ? new $throwable($message) : $throwable;
		ExceptionHandler::renderDebug($throwable, $unhandled);
	}
}

if (!function_exists('deprecated')) {
   function deprecated(string $message): void
   {
      trigger_error($message, E_USER_DEPRECATED);
   }
}

if (!function_exists('throw_if')) {
	/**
	 * @throws Throwable
	 */
	function throw_if(bool $bool, Throwable|string $throwable, string|null $message = ''): void
   {
      if ($bool === true) {
		 throw (is_string($throwable) ? new $throwable($message) : $throwable);
      }
   }
}

if (!function_exists('throw_unless')) {
	/**
	 * @throws Throwable
	 */
	function throw_unless(bool $bool, Throwable|string $throwable, string|null $message = ''): void
   {
      if ($bool === false) {
		  throw (is_string($throwable) ? new $throwable($message) : $throwable);
      }
   }
}

if (!function_exists('retry')) {
	function retry(int $attempts, callable $action, callable|int $delay = 100, callable|null $filter = null, mixed $fallback = null): mixed
	{
		// Inspired by the Laravel retry() helper.
		$throwable = null;
		$value = null;

		for ($tries = 1; $tries < $attempts + 1; $tries++) {
			try {
				$value = $action();
			} catch (Throwable $e) {
				if ($filter === null || (is_callable($filter) && $filter($e))) {
					if ($tries === $attempts) {
						$throwable = $e;
					}
					$delay = is_callable($delay) ? $delay($tries) : $delay;
					usleep($delay * 1000);
					continue;
				}
			}
			break;
		}

		if ($throwable instanceof Throwable) {
			ExceptionHandler::logThrowable($throwable);
			return $fallback;
		} else {
			return $value;
		}
	}
}

if (!function_exists('identity_has')) {
	function identity_has(array $conditions): bool
	{
		$provider = AuthManager::activeProvider();

		if ($provider->guest() || $provider === null) {
			return false;
		}

		foreach ($conditions as $key => $value) {
			if ($key === 'role') {
				$conditions['roles'] = [$value];
				continue;
			}
			if ($key === 'permission') {
				$conditions['permissions'] = [$value];
				continue;
			}
			$conditions[$key] = $value;
		}

		if (isset($conditions['roles']) && $provider->identity()->hasRole($conditions['roles']) === false) {
			return false;
		}

		if (isset($conditions['permissions'])) {
			if ($provider->identity()->hasPermission($conditions['permissions']) === false) {
				if ($provider->identity()->getRole()->hasPermission($conditions['permissions']) === false) {
					return false;
				}
			}
		}

		return true;
	}
}

if (!function_exists('user_has')) {
	/**
	 * This function will always return false if there's no authenticated User instance.
	 */
	function user_has(array $conditions): bool
	{
		$provider = AuthManager::activeProvider();

		if ($provider instanceof SessionAuthentication) {
			if (identity_has($conditions) === false || $provider->user() === null) {
				return false;
			}

			foreach ($conditions as $value) {
				if (is_string($value)) {
					$conditions[$value] = true;
				}
			}

			if (isset($conditions['verified_session']) && $provider->hasVerifiedSession() !== $conditions['verified_session']) {
				return false;
			}

			if (isset($conditions['verified_email']) && $provider->user()->email_verified !== $conditions['verified_email']) {
				return false;
			}

			if (isset($conditions['twofactor_enabled']) && $provider->user()->hasTwoFactorMethods() !== $conditions['twofactor_enabled']) {
				return false;
			}

			if (isset($conditions['twofactor_method']) && $provider->user()->hasTwoFactorMethod($conditions['twofactor_method']) === false) {
				return false;
			}

			return true;
		}
		return false;
	}
}

if (!function_exists('token_has')) {
	/**
	 * Should only be used in combination with a Provider using the TokenAuthentication interface. Will return false if there's no authenticated token.
	 */
	function token_has(array $conditions): bool
	{
		$provider = AuthManager::activeProvider();

		if ($provider instanceof TokenAuthentication) {
			if ($provider->getToken() === null) {
				return false;
			}

			foreach ($conditions as $key => $value) {
				if ($value === 'internal') {
					$conditions[$value] = true;
				}
				if ($key === 'endpoint') {
					$conditions['endpoints'] = [$value];
				}
			}

			if (isset($conditions['endpoints']) && $provider->getToken()->hasEndpoint($conditions['endpoints']) === false) {
				return false;
			}

			if (isset($conditions['internal']) && $provider->getToken()->internal !== $conditions['internal']) {
				return false;
			}

			return true;
		}
		return false;
	}
}

// -----------------
// Utility Helpers

if (!function_exists('is_odd')) {
	function is_odd(int|float $value): bool
	{
		return $value & 1;
	}
}

if (!function_exists('is_even')) {
	function is_even(int|float $value): bool
	{
		return is_odd($value) === false;
	}
}

if (!function_exists('domain')) {
	function domain(bool $include_scheme = false): string
	{
		$host = RequestManager::getRequest()->targetHost();
		return $include_scheme ? sprintf('%s://%s', RequestManager::getRequest()->scheme(), $host) : $host;
	}
}

if (!function_exists('url')) {
	function url(string|null $path = null, array $query = []): UrlBuilder
	{
		$builder = new UrlBuilder();
		return $path === null ? $builder : $builder->foreign($path, $query);
	}
}

if (!function_exists('route')) {
	function route(string $name, array $params = [], array $query = []): UrlBuilder
	{
		$builder = new UrlBuilder();
		return $builder->route($name, $params, $query);
	}
}

if (!function_exists('csrf_input')) {
	function csrf_input(): string
	{
		$token_name = AccessManager::getCsrfTokenName();
		$token_value = AccessManager::getCsrfToken();
		return sprintf('<input type="hidden" value="%s" name="%s"/>', $token_value, $token_name);
	}
}

if (!function_exists('csrf_token')) {
	function csrf_token(): string
	{
		return AccessManager::getCsrfToken();
	}
}

if (!function_exists('csrf_token_name')) {
	function csrf_token_name(): string
	{
		return AccessManager::getCsrfTokenName();
	}
}

if (!function_exists('form_submit_time')) {
	function form_submit_time(): float
	{
		return microtime(true) - RequestManager::getRequest()->float('submit_timestamp');
	}
}

if (!function_exists('form_submit_time_allowed')) {
	function form_submit_time_allowed(): bool
	{
		$submit_time = form_submit_time();
		$submit_time_min = registry()->float('form_submit_time_min');
		$submit_time_max = registry()->float('form_submit_time_max');

		return $submit_time > $submit_time_min && $submit_time < $submit_time_max;
	}
}

// -----------------
// Internal

if (!function_exists('base_path')) {
	function base_path(string $path = ''): string
	{
		return strlen($path) > 0 ? getcwd().'/'.ltrim($path, '/') : getcwd();
	}
}

if (!function_exists('value_retriever')) {
	function value_retriever(mixed $value): callable
	{
		// Inspired by the Laravel valueRetriever() method.
		if (!is_string($value) && is_callable($value)) {
			return $value;
		}

		return function ($item) use ($value) {
			return data_get($item, $value);
		};
	}
}

if (!function_exists('convert_to_array')) {
	function convert_to_array(mixed $value): array
	{
		return match(true) {
			$value === null => [],
			is_array($value) => $value,
			$value instanceof Arrayable => $value->toArray(),
			$value instanceof JsonSerializable => convert_to_array($value->jsonSerialize()),
			$value instanceof Data => $value->export(),
			default => [$value],
		};
	}
}

if (!function_exists('data_get')) {
   function data_get(mixed $target, string|array|null $key, mixed $default = null): mixed
   {
      // Inspired by the Laravel data_get() helper.
      if ($key === null) {
         return $target;
      }

      $key = is_array($key) ? $key : explode('.', $key);

      foreach ($key as $i => $segment) {
         unset($key[$i]);

         if ($segment === null) {
            return $target;
         }

         if ($segment === '*') {
            if ($target instanceof Arrayable) {
               $target = $target->toArray();
            } elseif (!is_iterable($target)) {
               return $default;
            }

            $result = [];
            foreach ($target as $item) {
               $result[] = data_get($item, $key);
            }

            return in_array('*', $key) ? Arr::collapse($result) : $result;
         }

		 $target = match (true) {
			 $target instanceof ArrayAccess => $target->offsetGet($segment),
			 is_object($target) && isset($target->{$segment}) => $target->{$segment},
			 is_object($target) && method_exists($target, $segment) => $target->{$segment}(),
			 is_array($target) && array_key_exists($segment, $target) => $target[$segment],
			 default => null,
		 };

		 if ($target === null) {
			 return $default;
		 }
      }

      return $target;
   }
}

if (!function_exists('cookie_domain')) {
   function cookie_domain(): string
   {
      return (defined('COOKIE_DOMAIN')) ? COOKIE_DOMAIN : $_SERVER['SERVER_NAME'];
   }
}

if (!function_exists('hash_length')) {
	function hash_length(string $algorithm): int|null
	{
		return match ($algorithm) {
			'md5' => 32,
			'sha1' => 40,
			'sha256' => 64,
			'sha384' => 96,
			'sha512' => 128,
			default => null,
		};
	}
}

if (!function_exists('json_encode_clean')) {
	function json_encode_clean(mixed $value, int $depth = 512): false|string
	{
		return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK, $depth);
	}
}