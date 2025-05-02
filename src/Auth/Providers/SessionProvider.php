<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Auth\Providers;

use Rovota\Core\Auth\TrustedClient;
use Throwable;

class SessionProvider extends Provider implements SessionAuthentication
{

	public function authenticate(): bool
	{
		$this->loadTrustedClients();

		return true;
	}

	// -----------------

	public function trustClient(array $attributes = [], Identity|null $identity = null): void
	{
		$identity = $identity ?? $this->identity;
		$attributes['expiration'] = $attributes['expiration'] ?? now()->addDays(Registry::int('identity_trusted_client_duration', 30));
		$client = TrustedClient::create($identity, $attributes);

		if ($client->save()) {
			$name = Registry::string('identity_trusted_client_name', 'trusted_client').':'.hash('sha256', $identity->getName());
			CookieManager::queue($name, $client->hash, ['expires' => $attributes['expiration']]);

			$this->trusted_clients[$name] = $client;
		}
	}

	// -----------------

	protected function loadTrustedClients(): void
	{
		$cookies = CookieManager::getReceived();

		foreach ($cookies as $cookie) {

			if (str_starts_with($cookie->name, Registry::string('identity_trusted_client_name', 'trusted_client')) === false) {
				continue;
			}

			if ($cookie instanceof \Rovota\Core\Cookie\Cookie) {
				if (Str::length($cookie->value) !== 80) {
					$cookie->expire();
				} else {
					try {
						$trusted_client = TrustedClient::where(['hash' => $cookie->value])->when(registry('identity_trusted_client_ip_lock', false), function (Query $query) {
							return $query->where(['ips' => RequestManager::getRequest()->ip()]);
						})->first();
						if ($trusted_client instanceof TrustedClient && ($trusted_client->expiration === null || $trusted_client->expiration->isFuture())) {
							$this->trusted_clients[$cookie->name] = $trusted_client;
						} else {
							$cookie->expire();
						}
					} catch (Throwable $throwable) {
						ExceptionHandler::addThrowable($throwable);
					}
				}
			}

		}

	}

}