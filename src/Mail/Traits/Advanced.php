<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Mail\Traits;

use Rovota\Core\Localization\Language;
use Rovota\Core\Localization\LocalizationManager;
use Rovota\Core\Mail\Enums\Priority;
use Rovota\Core\Support\Text;

trait Advanced
{

	private Priority $priority = Priority::Normal;

	private string $type = 'regular';

	private Language $language;

	private array $headers = [];

	// -----------------

	public function priority(Priority $priority): static
	{
		$this->priority = $priority;
		return $this;
	}

	public function type(string $type): static
	{
		$this->type = $type;
		return $this;
	}

	// -----------------

	public function language(Language|string|int $identifier): static
	{
		if ($identifier instanceof Language) {
			$this->language = $identifier;
		}
		$this->language = LocalizationManager::getLanguage($identifier);
		return $this;
	}

	// -----------------

	public function unsubscribe(string|null $email = null, string|null $url = null): static
	{
		if ($email !== null) {
			$email = sprintf('<mailto: %s>', $email);
		}
		if ($url !== null) {
			$url = sprintf('<%s>', $url);
		}
		$this->header('List-Unsubscribe', implode(', ', [$email, $url]));
		return $this;
	}

	// -----------------

	public function header(string $name, string $value): static
	{
		if (Text::length($name) > 0 && Text::length($value) > 0) {
			$this->headers[$name] = $value;
		}
		return $this;
	}

	public function headers(array $headers): static
	{
		foreach ($headers as $name => $value) {
			$this->headers[$name] = $value;
		}
		return $this;
	}

	public function withoutHeader(string $name): static
	{
		unset($this->headers[$name]);
		return $this;
	}

	public function withoutHeaders(array $names = []): static
	{
		if (empty($names)) {
			$this->headers = [];
		} else {
			foreach ($names as $name) {
				unset($this->headers[$name]);
			}
		}
		return $this;
	}

}