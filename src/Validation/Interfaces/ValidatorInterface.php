<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Validation\Interfaces;

use Rovota\Core\Structures\Bucket;
use Rovota\Core\Structures\ErrorBucket;

interface ValidatorInterface
{

	public static function create(mixed $data, array $rules): static;

	// -----------------

	public function succeeds(): bool;

	public function fails(): bool;

	// -----------------

	public function clear(): static;

	public function addRule(string $attribute, string $name, array|string $options = []): static;

	public function addData(string $name, mixed $value): static;

	// -----------------

	public function safe(): Bucket;

	// -----------------

	public function errors(): ErrorBucket;

	// -----------------

	public function withErrors(ErrorBucket|array $errors): static;

	// -----------------

	public function setMessageOverride(string $type, string $identifier, string $message): static;

	public function setMessageOverrides(array $messages): static;

	public function findMessageOverride(string $type, string $identifier): string|null;

	// -----------------

	public function clearMessageOverrides(): void;

}