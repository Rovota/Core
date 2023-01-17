<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Mail\Interfaces;

use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Localization\Language;
use Rovota\Core\Mail\Enums\Encoding;
use Rovota\Core\Mail\Enums\Priority;
use Rovota\Core\Storage\Interfaces\FileInterface;
use Rovota\Core\Views\View;

interface Mailable
{

	public function deliver(): bool;

	// -----------------

	public function from(string $name, string $address): static;

	public function replyTo(string $name, string $address): static;

	public function to(Identity|string|int|array $name, string|null $address = null): static;

	// -----------------

	public function priority(Priority $priority): static;

	public function type(string $type): static;

	public function language(Language|string|int $identifier): static;

	public function unsubscribe(string|null $email = null, string|null $url = null): static;

	public function header(string $name, string $value): static;

	public function headers(array $headers): static;

	public function withoutHeader(string $name): static;

	public function withoutHeaders(array $names = []): static;

	// -----------------

	public function subject(string $content): static;

	public function summary(string $content): static;

	public function plain(string $content): static;

	public function view(View|string $name, string|null $source = null): static;

	public function text(string $content): static;

	public function with(string $name, mixed $data): static;

	// -----------------

	public function attachment(FileInterface|string $file, string|null $name = null, string|null $mime_type = null, Encoding $encoding = Encoding::UTF8): static;

	public function stringAttachment(string $content, string $name, string $mime_type, Encoding $encoding = Encoding::UTF8): static;

	// -----------------

	/**
	 * @throws \Rovota\Core\Views\Exceptions\MissingViewException
	 */
	public function render(): string|null;

	// -----------------

	public function hasFrom(): bool;

	public function hasReplyTo(): bool;

	public function hasReceiver(): bool;

	public function hasSubject(): bool;

	public function hasSummary(): bool;

	public function hasView(): bool;

	public function hasVariable(string $name): bool;

	public function hasUnsubscribe(): bool;

	public function hasHeader(string $name): bool;

}