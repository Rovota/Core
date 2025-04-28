<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Storage\Drivers;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Rovota\Core\Storage\Disk;
use Rovota\Core\Storage\DiskConfig;

final class S3 extends Disk
{

	public function __construct(string $name, DiskConfig $config)
	{
		$parameters = [
			'credentials' => [
				'key' => $config->parameters->get('key'),
				'secret' => $config->parameters->get('secret'),
			],
			'region' => $config->parameters->get('region'),
			'version' => $config->parameters->get('version', 'latest'),
			'http' => [
				'connect_timeout' => 5,
			],
			'signature_version' => 'v4',
		];

		if ($config->parameters->has('endpoint')) {
			$parameters['endpoint'] = $config->parameters->get('endpoint');
		}

		$client = new S3Client($parameters);
		$adapter = new AwsS3V3Adapter($client, $config->parameters->get('bucket'), $config->root);

		parent::__construct($name, $adapter, $config);
	}

	// -----------------

	public function bucket(): string
	{
		return $this->config->parameters->get('bucket');
	}

	public function key(): string
	{
		return $this->config->parameters->get('key');
	}

	public function secret(): string
	{
		return $this->config->parameters->get('secret');
	}

	public function region(): string
	{
		return $this->config->parameters->get('region');
	}

	public function version(): string
	{
		return $this->config->parameters->get('version', 'latest');
	}

	public function endpoint(): string
	{
		return $this->config->parameters->get('endpoint');
	}

}