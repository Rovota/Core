<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Mail\Traits;

use Rovota\Core\Mail\Enums\Encoding;
use Rovota\Core\Storage\Interfaces\FileInterface;
use Rovota\Core\Storage\StorageManager;
use Rovota\Core\Support\Str;
use Rovota\Core\Validation\ValidationManager;
use Rovota\Core\Views\View;
use Rovota\Core\Views\ViewManager;

trait Contents
{

	private string|null $subject = null;
	private string|null $plain = null;
	private string|null $text = null;

	private View|array|null $view = null;

	private array $variables = [];
	private array $attachments = [];

	// -----------------

	public function subject(string $content): static
	{
		$this->subject = trim($content);
		return $this;
	}

	public function summary(string $content): static
	{
		$this->variables['mail_summary'] = $content;
		return $this;
	}

	// -----------------

	public function plain(string $content): static
	{
		$this->plain = trim($content);
		return $this;
	}

	public function view(View|string $name, string|null $source = null): static
	{
		if ($name instanceof View) {
			$this->view = $name;
		} else {
			$this->view = ['name' => $name,'source' => $source];
		}
		return $this;
	}

	public function text(string $content): static
	{
		$this->text = trim($content);
		return $this;
	}

	public function with(string $name, mixed $data): static
	{
		$this->variables[$name] = $data;
		return $this;
	}

	// -----------------

	/**
	 * @throws \League\Flysystem\FilesystemException
	 */
	public function attachment(FileInterface|string $file, string|null $name = null, string|null $mime_type = null, Encoding $encoding = Encoding::UTF8): static
	{
		if (is_string($file)) {
			$file = StorageManager::get()->file($file);
		}

		if ($file instanceof FileInterface) {
			$name = $name ?? (sprintf('%s.%s', $file->properties()->name, $file->properties()->extension));
			$this->stringAttachment($file->contents, $name, $mime_type ?? $file->properties()->mime_type, $encoding);
		}

		return $this;
	}

	public function stringAttachment(string $content, string $name, string $mime_type, Encoding $encoding = Encoding::UTF8): static
	{
		if (Str::contains($name, '.') === false) {
			$extensions = ValidationManager::mimeTypeExtensions($mime_type);
			$name = Str::finish($name.'.', empty($extensions) ? 'txt' : $extensions[0]);
		}

		$this->attachments[] = [
			'content' => trim($content),
			'name' => trim($name),
			'encoding' => $encoding->value,
			'mime_type' => $mime_type,
		];

		return $this;
	}

	// -----------------

	public function render(): string|null
	{
		$content = null;
		$view = null;

		if ($this->plain !== null) {
			$content = $this->plain;
			$this->mailer->isHTML(false);
			foreach ($this->variables as $name => $value) {
				$content = str_replace(sprintf('{%% %s %%}', $name), $value, $content);
			}
		}

		if (is_array($this->view)) {
			$view = ViewManager::makeMail($this->view['name'], $this->view['source'], $this->language);
		} else if ($this->view instanceof View) {
			$view = $this->view;
		}

		if ($view instanceof View) {
			$this->mailer->isHTML();
			foreach ($this->variables as $name => $value) {
				$this->view->with($name, $value);
			}
			$content = $this->view->render();
		}

		return $content;
	}

}