<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Mail;

use ArrayAccess;
use Countable;
use Iterator;
use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Auth\User;
use Rovota\Core\Mail\Interfaces\Mailable;
use Rovota\Core\Support\Arr;
use Rovota\Core\Support\Collection;
use Rovota\Core\Support\Interfaces\Arrayable;
use Rovota\Core\Support\Traits\Conditionable;
use Rovota\Core\Support\Traits\Macroable;

class MailingQueue implements ArrayAccess, Iterator, Countable, Arrayable
{
	use Macroable, Conditionable;

	protected array $receivers = [];
	protected array $keys = [];

	protected int $success = 0;
	protected int $failure = 0;

	protected Mailable|null $mailable = null;

	protected int $position = 0;

	// -----------------

	public function __construct(mixed $receivers = [])
	{
		$this->receivers = convert_to_array($receivers);
		$this->keys = array_keys($this->receivers);
	}

	// -----------------

	public static function make(mixed $receivers): static
	{
		return new static($receivers);
	}

	/**
	 * @throws \Envms\FluentPDO\Exception
	 */
	public static function createFromList(MailingList|string $list): static
	{
		$list = $list instanceof MailingList ? $list : MailingList::where('name', $list)->first();

		if ($list === null) {
			return new static();
		}

		/** @var Collection $users */
		/** @var Collection $guests */
		[$users, $guests] = $list->subscribers->partition(function (MailingListSubscriber $subscriber) {
			return $subscriber->user_id !== null;
		});

		$users = User::whereIn('id', $users->pluck('user_id'))->get();
		$guests = $guests->transform(function (MailingListSubscriber $subscriber) {
			return ['name' => $subscriber->name, 'address' => $subscriber->address];
		});

		return new static(array_merge($users->toArray(), $guests->toArray()));
	}

	// -----------------

	public function add(Identity|string|int|array $name, string|null $address = null): MailingQueue
	{
		if (is_array($name)) {
			foreach ($name as $key => $data) {
				$this->add($key, $data);
			}
		} else {
			$this->offsetSet(null, $name instanceof Identity ? $name : ['name' => $name, 'address' => $address]);
		}
		return $this;
	}

	public function remove(array|string $addresses): MailingQueue
	{
		if (is_string($addresses)) {
			$addresses = [$addresses];
		}

		foreach ($this->receivers as $key => $receiver) {
			if ($receiver instanceof Identity && Arr::contains($addresses, $receiver->getEmail())) {
				$this->offsetUnset($key);
			}
			if (is_array($receiver) && Arr::contains($addresses, $receiver['address'])) {
				$this->offsetUnset($key);
			}
		}

		return $this;
	}

	/**
	 * Returns the receivers from the queue that pass a given truth test.
	 */
	public function filter(callable $callback): static
	{
		return new static(Arr::filter($this->receivers, $callback));
	}

	/**
	 * Returns all receivers from the queue except those that pass the truth test.
	 */
	public function reject(callable $callback): static
	{
		$new = [];
		foreach ($this->receivers as $key => $value) {
			if ($callback($value, $key) === false) {
				$new[$key] = $value;
			}
		}
		return new static($new);
	}

	// -----------------

	public function send(Mailable|null $mailable = null): bool
	{
		$this->deliverBatch($this->receivers, $mailable ?? $this->mailable);
		return true;
	}

	/**
	 * Iterates over all receivers in the queue and passes the mailable and receiver to the given callback. Stops iterating when `false` is returned.
	 */
	public function sendEach(callable $callback, Mailable|null $mailable = null): bool
	{
		$mailable = $mailable ?? $this->mailable;
		foreach ($this->receivers as $name => $address) {
			$receiver = $address instanceof Identity ? $address : ['name' => $name, 'address' => $address];
			$mailable = clone $mailable;
			if ($callback($mailable, $receiver) === false) {
				break;
			}
			$this->deliver($receiver, $mailable);
		}
		return true;
	}

	// -----------------

	// TODO: Chunked delivery, which sends only an X amount at a time.

	// -----------------

	protected function deliver(mixed $receiver, Mailable $mailable): bool
	{
		if ($receiver instanceof Identity) {
			$mailable->to($receiver);
		} else {
			$mailable->to($receiver['name'], $receiver['address']);
		}
		return $mailable->deliver();
	}

	protected function deliverBatch(array $receivers, Mailable $mailable): bool
	{
		$mailable->to($receivers);
		return $mailable->deliver();
	}

	// -----------------

	public function toArray(): array
	{
		return $this->receivers;
	}

	public function current(): mixed
	{
		return $this->receivers[$this->keys[$this->position]];
	}

	public function next(): void
	{
		++$this->position;
	}

	public function key(): mixed
	{
		return $this->keys[$this->position];
	}

	public function valid(): bool
	{
		return isset($this->keys[$this->position]);
	}

	public function rewind(): void
	{
		$this->position = 0;
	}

	public function offsetExists(mixed $offset): bool
	{
		return isset($this->receivers[$offset]);
	}

	public function offsetGet(mixed $offset): mixed
	{
		return $this->receivers[$offset] ?? null;
	}

	public function offsetSet(mixed $offset, mixed $value): void
	{
		if (is_null($offset)) {
			$this->receivers[] = $value;
			$this->keys[] = array_key_last($this->receivers);
		} else {
			$this->receivers[$offset] = $value;
			if (!in_array($offset, $this->keys)) $this->keys[] = $offset;
		}
	}

	public function offsetUnset(mixed $offset): void
	{
		unset($this->receivers[$offset]);
		unset($this->keys[array_search($offset, $this->keys)]);
		$this->keys = array_values($this->keys);
	}

	public function count(): int
	{
		return count($this->receivers);
	}

}