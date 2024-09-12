<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

use League\Flysystem\FilesystemException;
use Rovota\Core\Auth\AccessManager;
use Rovota\Core\Auth\ApiToken;
use Rovota\Core\Auth\AuthManager;
use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Auth\Interfaces\SessionAuthentication;
use Rovota\Core\Auth\Interfaces\TokenAuthentication;
use Rovota\Core\Auth\User;
use Rovota\Core\Cookie\Cookie;
use Rovota\Core\Cookie\CookieManager;
use Rovota\Core\Http\RequestManager;
use Rovota\Core\Partials\Partial;
use Rovota\Core\Partials\PartialManager;
use Rovota\Core\Routing\UrlBuilder;
use Rovota\Core\Session\SessionManager;
use Rovota\Core\Storage\Interfaces\FileInterface;
use Rovota\Core\Storage\StorageManager;
use Rovota\Core\Support\ValidationTools;
use Rovota\Core\Views\Exceptions\MissingViewException;
use Rovota\Core\Views\View;
use Rovota\Core\Views\ViewManager;

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
	/**
	 * @deprecated Use asset_url() instead.
	 */
	function asset(string $path, array $query = [], string|null $disk = null): UrlBuilder|null
	{
		return asset_url($path, $query, $disk);
	}
}

if (!function_exists('asset_url')) {
	function asset_url(string $path, array $query = [], string|null $disk = null): UrlBuilder|null
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
// Misc

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

			if (isset($conditions['2fa_enabled']) && $provider->user()->hasTwoFactorMethods() !== $conditions['2fa_enabled']) {
				return false;
			}

			if (isset($conditions['2fa_method']) && $provider->user()->hasTwoFactorMethod($conditions['2fa_method']) === false) {
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

if (!function_exists('security_csrf_token')) {
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