<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http;

use Rovota\Core\Cookie\CookieManager;
use Rovota\Core\Facades\Registry;
use Rovota\Core\Storage\Interfaces\FileInterface;
use Rovota\Core\Support\ImageObject;
use Throwable;

class Response
{

	protected function prepareForPrinting(): void
	{
		CookieManager::applyQueue();
	}

	// -----------------

	protected function getContentFromFile(): string
	{
		/** @var FileInterface $file */
		$file = $this->content;

		if (str_contains($file->properties()->mime_type, 'image') && extension_loaded('imagick')) {
			return $this->getProcessedImage($file->asImage());
		}

		$this->setContentType($file->properties()->mime_type);
		return $file->asString();
	}

	protected function getContentFromImage(): string
	{
		if (extension_loaded('imagick')) {
			return $this->getProcessedImage($this->content);
		}

		$this->setContentType($this->content->mime_type);
		return (string)$this->content;
	}

	protected function getContentFromView(): string
	{
		$this->setContentType('text/html');
		return $this->content->render();
	}

	// -----------------

	protected function getProcessedImage(ImageObject $image): string
	{
		try {

			// Verify WebP support and convert if needed
			if (RequestManager::getRequest()->acceptsWebP()) {
				if (Registry::bool('media_convert_to_webp', true) && $image->mime_type !== 'image/webp') {
					$image->mime_type = 'image/webp';
					$image->format('webp');
					if (isset($this->headers['Content-Disposition'])) {
						$this->headers['Content-Disposition'] = str_replace($image->format, 'webp', $this->headers['Content-Disposition']);
					}
				}
			} else {
				if (Str::contains($image->mime_type, 'webp')) {
					$image->mime_type = 'image/jpeg';
					$image->flatten('white');
					$image->format('jpg');
				}
			}

			// Safely set content type now
			$this->setContentType($image->mime_type);

			// Remove EXIF data
			if (Registry::bool('media_preserve_exif', true) === false) {
				$image->removeExif();
			}

			// Resize to fit height and width or variant
			if ($this->variant !== null) {
				$dimensions = Registry::array('media_size_'.$this->variant);
				if (empty($dimensions) === false) {
					$image->resize($dimensions[0], $dimensions[1]);
				}
			} else if (is_array($this->dimensions)) {
				$width = $this->dimensions[0] ?? $this->dimensions[1] * 10;
				$height = $this->dimensions[1] ?? $this->dimensions[0] * 10;
				$image->resize($width, $height);
			}

			// Compress
			$compression_level = Registry::int('media_compression_level');
			if ($compression_level > 0) {
				$image->compress($compression_level);
			}

			return $image->extract();

		} catch (Throwable $throwable) {
			ExceptionHandler::logThrowable($throwable);
			$this->setContentType($image->mime_type);
			return (string)$image;
		}
	}

}