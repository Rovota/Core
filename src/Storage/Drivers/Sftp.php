<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Storage\Drivers;

use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use Rovota\Core\Storage\Disk;
use Rovota\Core\Storage\DiskConfig;

final class Sftp extends Disk
{

	public function __construct(string $name, DiskConfig $config)
	{
		$parameters = [
			'host' => $config->parameters->get('host'),
			'username' => $config->parameters->get('username'),
			'password' => $config->parameters->get('password'),

			'private_key' => $config->parameters->get('private_key'),
			'passphrase' => $config->parameters->get('passphrase'),

			'port' => $config->parameters->int('port', 22),
			'agent' => $config->parameters->get('agent', false),
			'timeout' => $config->parameters->int('timeout', 10),
			'retries' => $config->parameters->int('retries', 4),
			'fingerprint' => $config->parameters->get('fingerprint'),
		];

		$visibility = PortableVisibilityConverter::fromArray($config->parameters->array('visibility', [
			'file' => ['public' => 0640, 'private' => 0604],
			'dir' => ['public' => 0740, 'private' => 7604],
		]));

		$provider = new SftpConnectionProvider(...array_values($parameters));
		$adapter = new SftpAdapter($provider, $config->root, $visibility);

		parent::__construct($name, $adapter, $config);
	}

	// -----------------

	public function host(): string
	{
		return $this->config->parameters->get('host');
	}

	public function username(): string
	{
		return $this->config->parameters->get('username');
	}

	public function password(): string|null
	{
		return $this->config->parameters->get('password');
	}

	public function privateKey(): string|null
	{
		return $this->config->parameters->get('private_key');
	}

	public function passphrase(): string|null
	{
		return $this->config->parameters->get('passphrase');
	}

	public function port(): int
	{
		return $this->config->parameters->get('port', 22);
	}

	public function agent(): bool
	{
		return $this->config->parameters->get('agent', false);
	}

	public function timeout(): int
	{
		return $this->config->parameters->get('timeout', 10);
	}

	public function retries(): int
	{
		return $this->config->parameters->get('retries', 4);
	}

	public function fingerprint(): string|null
	{
		return $this->config->parameters->get('fingerprint');
	}

}