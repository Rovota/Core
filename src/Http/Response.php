<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Http;

use BackedEnum;
use JsonSerializable;
use Rovota\Core\Cookie\CookieManager;
use Rovota\Core\Facades\Registry;
use Rovota\Core\Http\Enums\StatusCode;
use Rovota\Core\Http\Traits\ResponseModifiers;
use Rovota\Core\Kernel\ExceptionHandler;
use Rovota\Core\Storage\Enums\MediaType;
use Rovota\Core\Storage\File;
use Rovota\Core\Storage\Media;
use Rovota\Core\Support\FluentString;
use Rovota\Core\Support\ImageObject;
use Rovota\Core\Support\Str;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\Traits\Macroable;
use Rovota\Core\Views\View;
use Throwable;

class Response
{
	use Macroable, ResponseModifiers, Conditionable;

	protected mixed $content;

	// -----------------

	public function __construct(array $headers, mixed $content, StatusCode $code)
	{
		$this->headers = $headers;
		$this->content = $content;
		$this->status_code = $code;
	}

	public function __toString(): string
	{
		$this->prepareForPrinting();
		return $this->content ?? '';
	}

	// -----------------

	protected function prepareForPrinting(): void
	{
		ob_end_clean();

		$content = match(true) {
			is_int($this->content) => $this->content,
			$this->content instanceof BackedEnum => $this->content->value,
			$this->content instanceof File => $this->getContentFromFile(),
			$this->content instanceof ImageObject => $this->getContentFromImage(),
			$this->content instanceof Media => $this->getContentFromMedia(),
			$this->content instanceof FluentString => $this->getContentFromFluentString(),
			$this->content instanceof View => $this->getContentFromView(),
			$this->content instanceof JsonSerializable || is_array($this->content) => $this->getContentAsJson(),
			default => $this->getContentAsString(),
		};

		if ($this->content instanceof StatusCode || (is_int($content) && StatusCode::tryFrom($content) instanceof BackedEnum)) {
			http_response_code($content);
			$content = null;
		} else {
			http_response_code($this->status_code->value);
		}

		foreach ($this->headers as $key => $value) {
			header(sprintf('%s: %s', $key, $value));
		}

		CookieManager::applyQueue();

		$this->content = $content;
	}

	// -----------------

	protected function getContentAsJson(): string
	{
		$this->setContentType('application/json; charset=UTF-8');
		return json_encode_clean($this->content);
	}

	protected function getContentAsString(): string
	{
		return (string)$this->content;
	}

	// -----------------

	protected function getContentFromFile(): string
	{
		if (str_contains($this->content->mime_type, 'image') && extension_loaded('imagick')) {
			return $this->getProcessedImage($this->content->asImage());
		}

		$this->setContentType($this->content->mime_type);
		return $this->content->asString();
	}

	protected function getContentFromImage(): string
	{
		if (extension_loaded('imagick')) {
			return $this->getProcessedImage($this->content);
		}

		$this->setContentType($this->content->mime_type);
		return (string)$this->content;
	}

	protected function getContentFromMedia(): string
	{
		if ($this->content->type === MediaType::Image) {
			$variant = $this->variant;
			$this->variant = null;
			return $this->getProcessedImage($this->content->asImage($variant));
		}

		$this->setContentType($this->content->mime_type);
		return $this->content->asString();
	}

	protected function getContentFromFluentString(): string
	{
		return (string) $this->content;
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
			if (request()->acceptsWebP()) {
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