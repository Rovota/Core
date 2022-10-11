<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     Rovota License
 */

namespace Rovota\Core\Auth;

use Rovota\Core\Auth\Enums\TokenStatus;
use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Database\Model;
use Rovota\Core\Http\Throttling\Enums\PeriodType;
use Rovota\Core\Support\Arr;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Text;
use function now;

/**
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string|null $label
 * @property int $throttle_limit
 * @property PeriodType $throttle_period
 * @property array|null $endpoints
 * @property bool $internal
 * @property Moment $expiration
 * @property TokenStatus $status
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class ApiToken extends Model
{

	protected string|null $table = 'api_tokens';

	protected array $attributes = [
		'throttle_limit' => 200,
		'throttle_period' => PeriodType::Minute,
		'internal' => false,
		'status' => TokenStatus::Inactive,
	];

	protected array $casts = [
		'throttle_period' => ['enum', PeriodType::class],
		'endpoints' => 'array',
		'internal' => 'bool',
		'expiration' => 'moment',
		'status' => ['enum', TokenStatus::class],
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	protected bool $enable_composites = false;

	// -----------------

	public static function createUsing(Identity $identity, array $attributes = []): static
	{
		$attributes = array_merge([
			'user_id' => $identity->getId(),
			'token' => Text::random(80),
			'expiration' => now()->addYear(),
		], $attributes);

		return new static($attributes);
	}

	// -----------------

	public function hasEndpoint(array|string $endpoint): bool
	{
		$endpoints = is_array($endpoint) ? $endpoint : [$endpoint];

		if ($this->endpoints === null) {
			return true;
		}

		return Arr::containsAny($this->endpoints ?? [], array_merge($endpoints, ['*']));
	}

	// -----------------

	public function expire(): bool
	{
		$this->expiration = now();
		return $this->save();
	}

}