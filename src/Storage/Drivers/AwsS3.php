<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Storage\Drivers;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Rovota\Core\Storage\Disk;

final class AwsS3 extends Disk
{

	public function __construct(string $name, array $options = [])
	{
		$config = [
			'credentials' => [
				'key' => $options['key'],
				'secret' => $options['secret'],
			],
			'region' => $options['region'],
			'version' => $options['version'] ?? 'latest',
			'endpoint' => $options['endpoint'],
			'http' => [
				'connect_timeout' => 5,
			],
			'signature_version' => 'v4',
		];

		$adapter = new AwsS3V3Adapter(new S3Client($config), $options['bucket'], $options['root']);
		parent::__construct($name, $adapter, $options);
	}

	// -----------------

	public function bucket(): string
	{
		return $this->option('bucket');
	}

	public function key(): string
	{
		return $this->option('key');
	}

	public function secret(): string
	{
		return $this->option('secret');
	}

	public function region(): string
	{
		return $this->option('region');
	}

	public function version(): string
	{
		return $this->option('version') ?? 'latest';
	}

	public function endpoint(): string
	{
		return $this->option('endpoint');
	}

}