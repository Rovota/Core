<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Storage;

use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

final class RemoteDisk extends Disk
{

	public function __construct(string $name, array $options = [])
	{
		$config = [
			'host' => $options['host'],
			'username' => $options['username'],
			'password' => $options['password'] ?? null,
			'private_key' => $options['private_key'] ?? null,
			'passphrase' => $options['passphrase'] ?? null,
			'port' => $options['port'] ?? 22,
			'agent' => $options['agent'] ?? false,
			'timeout' => $options['timeout'] ?? 10,
			'retries' => $options['retries'] ?? 4,
			'fingerprint' => $options['fingerprint'] ?? null,
		];

		$visibility = PortableVisibilityConverter::fromArray($options['visibility'] ?? ['file' => ['public' => 0640, 'private' => 0604], 'dir' => ['public' => 0740, 'private' => 7604]]);

		$adapter = new SftpAdapter(new SftpConnectionProvider(...array_values($config)), $options['root'], $visibility);
		parent::__construct($name, $adapter, $options);
	}

	// -----------------

	public function host(): string
	{
		return $this->option('host');
	}

	public function username(): string
	{
		return $this->option('username');
	}

	public function password(): string|null
	{
		return $this->option('password');
	}

	public function privateKey(): string|null
	{
		return $this->option('private_key');
	}

	public function passphrase(): string|null
	{
		return $this->option('passphrase');
	}

	public function port(): int
	{
		return $this->option('port') ?? 22;
	}

	public function agent(): bool
	{
		return $this->option('agent') ?? false;
	}

	public function timeout(): int
	{
		return $this->option('timeout') ?? 10;
	}

	public function retries(): int
	{
		return $this->option('retries') ?? 4;
	}

	public function fingerprint(): string|null
	{
		return $this->option('fingerprint');
	}

}