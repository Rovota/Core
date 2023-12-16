<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Auth;

use Rovota\Core\Auth\Enums\SessionType;
use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Database\Model;
use Rovota\Core\Facades\Registry;
use Rovota\Core\Http\RequestManager;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Str;
use function now;

/**
 * @property int $id
 * @property int $user_id
 * @property string $ip
 * @property string $client
 * @property string $hash
 * @property Moment $expiration
 * @property bool $temporary
 * @property SessionType $type
 * @property bool $verified
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class Session extends Model
{

	protected string|null $table = 'sessions';

	protected array $attributes = [
		'type' => SessionType::Browser,
		'verified' => false,
	];

	protected array $casts = [
		'expiration' => 'moment',
		'type' => ['enum', SessionType::class],
		'verified' => 'bool',
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	protected bool $enable_composites = false;

	// -----------------

	public static function createUsing(Identity $identity, array $attributes = []): static
	{
		$duration = Registry::int('identity_session_duration');

		$attributes = array_merge([
			'user_id' => $identity->getId(),
			'ip' => RequestManager::getRequest()->ip(),
			'client' => RequestManager::getRequest()->client(),
			'hash' => Str::random(80),
			'expiration' => now()->addDays($duration === 0 ? 1 : $duration),
		], $attributes);

		if ($duration === 0) {
			$attributes['temporary'] = true;
		}

		return new static($attributes);
	}

	// -----------------

	public function expire(): bool
	{
		$this->expiration = now();
		return $this->save();
	}

	public function verify(): bool
	{
		$this->verified = true;
		return $this->save();
	}

}