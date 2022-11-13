<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Support;

use Imagick;
use ImagickException;
use ImagickPixel;
use ImagickPixelException;

class ImageObject
{

	public Imagick $image;
	public string $mime_type;
	public string $format;

	private bool $success = true;

	// -----------------

	public function __construct(string $content, string $format, string $mime_type)
	{
		$this->image = new Imagick();
		try {
			$this->mime_type = $mime_type;
			$this->format = $format;
			$this->image->setFormat($format);
			$this->image->readImageBlob($content);
		} catch (ImagickException) {
			$this->success = false;
		}
	}

	// -----------------

	public function __toString(): string
	{
		try {
			$content = $this->image->getImagesBlob();
		} catch (ImagickException) {
			return '';
		}
		return $content;
	}

	// -----------------

	public function isSuccess(): bool
	{
		return $this->success;
	}

	// -----------------

	/**
	 * @throws ImagickException
	 */
	public function extract(): string
	{
		$content = $this->image->getImagesBlob();
		$this->image->clear();
		return $content;
	}

	public function clear(): void
	{
		$this->image->clear();
	}

	/**
	 * @throws ImagickException
	 */
	public function resize(int $width, int $height): ImageObject
	{
		$this->image->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 0.0, true);
		return $this;
	}

	/**
	 * @throws ImagickException
	 * @throws ImagickPixelException
	 */
	public function flatten(string|null $background = null): ImageObject
	{
		if ($background !== null) {
			$this->image->setImageBackgroundColor(new ImagickPixel($background));
		}
		$this->image->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
		return $this;
	}

	/**
	 * @throws ImagickException
	 */
	public function format(string $format): ImageObject
	{
		$this->format = $format;
		if (Text::contains($this->mime_type, $format) === false) {
			$this->image->setFormat($format);
		}
		return $this;
	}

	/**
	 * @throws ImagickException
	 */
	public function compress(int $level): ImageObject
	{
		if ($level > 100) { $level = 100; }
		if ($level < 1) { $level = 1; }
		$this->image->setImageCompressionQuality($level);
		return $this;
	}

	/**
	 * @throws ImagickException
	 */
	public function removeExif(): ImageObject
	{
		$profiles = $this->image->getImageProfiles('ICC');
		$this->image->stripImage();
		if (empty($profiles) === false) {
			$this->image->profileImage('ICC', $profiles['ICC']);
		}
		return $this;
	}

}