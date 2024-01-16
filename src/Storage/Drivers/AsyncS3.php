<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage\Drivers;

use AsyncAws\S3\S3Client;
use League\Flysystem\AsyncAwsS3\AsyncAwsS3Adapter;
use Rovota\Core\Storage\Disk;
use Rovota\Core\Storage\DiskConfig;

final class AsyncS3 extends Disk
{

	public function __construct(string $name, DiskConfig $config)
	{
		$parameters = [
			'region' => $config->parameters->get('region'),
			'accessKeyId' => $config->parameters->get('key'),
			'accessKeySecret' => $config->parameters->get('secret'),
			'endpointDiscoveryEnabled' => true,
		];

		if ($config->parameters->has('endpoint')) {
			$parameters['endpoint'] = $config->parameters->get('endpoint');
		}

		$client = new S3Client($parameters);
		$adapter = new AsyncAwsS3Adapter($client, $config->parameters->get('bucket'), $config->root);

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