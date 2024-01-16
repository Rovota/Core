<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Storage\Enums;

use Rovota\Core\Support\Traits\EnumHelpers;

enum Driver: string
{
	use EnumHelpers;

	case Custom = 'custom';
	case Local = 'local';
	case AsyncS3 = 'async-s3';
	case S3 = 's3';
	case Sftp = 'sftp';

	// -----------------

	public static function isSupported(string $name): bool
	{
		$driver = self::tryFrom($name);

		if ($driver === null) {
			return false;
		}

		if ($driver === Driver::AsyncS3) {
			if (class_exists('League\Flysystem\AsyncAwsS3\AsyncAwsS3Adapter') === false) {
				return false;
			}
			if (class_exists('AsyncAws\S3\S3Client') === false) {
				return false;
			}
		}

		if ($driver === Driver::S3) {
			if (class_exists('League\Flysystem\AwsS3V3\AwsS3V3Adapter') === false) {
				return false;
			}
			if (class_exists('Aws\S3\S3Client') === false) {
				return false;
			}
		}

		if ($driver === Driver::Sftp) {
			if (class_exists('League\Flysystem\PhpseclibV3\SftpAdapter') === false) {
				return false;
			}
		}

		return true;
	}

	// -----------------

	public function label(): string
	{
		return match ($this) {
			Driver::Custom => 'Custom',
			Driver::Local => 'Filesystem',
			Driver::AsyncS3 => 'Async S3',
			Driver::S3 => 'S3',
			Driver::Sftp => 'SFTP',
		};
	}

	public function description(): string
	{
		return match ($this) {
			Driver::Custom => 'Specify your own storage adapter to use with this disk.',
			Driver::Local => 'Use a folder on the local filesystem as disk.',
			Driver::AsyncS3 => 'Use any S3-compatible cloud storage solution, asynchronously.',
			Driver::S3 => 'Use any S3-compatible cloud storage solution.',
			Driver::Sftp => 'Connect to a storage server using the SSH File Transfer Protocol.',
		};
	}

}