<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Http\Client\Traits;

trait ConfigModifiers
{

	public function set(array|string $options, mixed $value = null): static
	{
		if (is_string($options)) {
			$options = [$options => $value];
		}
		foreach ($options as $name => $value) {
			$this->config->set($name, $value);
		}
		return $this;
	}

	// -----------------

	public function header(string $name, string $value): static
	{
		$this->config->set('headers.'.$name, trim($value));
		return $this;
	}

	public function headers(array $headers): static
	{
		foreach ($headers as $name => $value) {
			$this->header($name, $value);
		}
		return $this;
	}

	public function useragent(string $useragent): static
	{
		$this->header('User-Agent', $useragent);
		return $this;
	}

	// -----------------

	public function token(string $token, string $type): static
	{
		$this->header('Authorization', sprintf('%s %s', $type, $token));
		return $this;
	}

	public function bearer(string $token): static
	{
		$this->token($token, 'Bearer');
		return $this;
	}

	public function auth(string $username, string $password, string $type = 'basic'): static
	{
		$this->config->set('auth', [$username, $password, $type]);
		return $this;
	}

	// -----------------

	public function json(array $data): static
	{
		$this->config->set('json', $data);
		return $this;
	}

	public function body(string $data): static
	{
		$this->config->set('body', trim($data));
		return $this;
	}

	public function query(array|string $parameters, mixed $value = null): static
	{
		if (is_string($parameters)) {
			$parameters = [$parameters => $value];
		}
		foreach ($parameters as $name => $value) {
			$this->config->set('query.'.$name, $value);
		}
		return $this;
	}

	// -----------------

	public function latestVersion(): static
	{
		$this->config->set('version', 3.0);
		return $this;
	}

	public function oldestVersion(): static
	{
		$this->config->set('version', 1.1);
		return $this;
	}

	public function version(int|float $version): static
	{
		$this->config->set('version', (float) $version);
		return $this;
	}

	// -----------------

	public function delay(int|float $milliseconds): static
	{
		$this->config->set('delay', $milliseconds);
		return $this;
	}

	public function connectTimeout(int|float $seconds): static
	{
		$this->config->set('timeout', (float) $seconds);
		return $this;
	}

	public function responseTimeout(int|float $seconds): static
	{
		$this->config->set('connect_timeout', (float) $seconds);
		return $this;
	}

}