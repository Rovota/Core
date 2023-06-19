<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Security;

use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Database\Model;
use Rovota\Core\Http\RequestManager;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Str;
use function now;

/**
 * @property int $id
 * @property int $user_id
 * @property string $ip
 * @property string $device
 * @property string $hash
 * @property string $code
 * @property int $uses
 * @property Moment|null $expiration
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class Token extends Model
{

	protected string|null $table = '_tokens';

	protected array $attributes = [
		'uses' => 0,
	];

	protected array $casts = [
		'expiration' => 'moment',
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	protected bool $enable_composites = false;

	// -----------------

	public static function createUsing(Identity $identity, array $attributes = []): static
	{
		$attributes = array_merge([
			'user_id' => $identity->getId(),
			'ip' => RequestManager::getRequest()->ip(),
			'client' => RequestManager::getRequest()->client(),
			'hash' => Str::random(100),
			'expiration' => now()->addMinutes(30),
		], $attributes);

		return new static($attributes);
	}

	// -----------------

	public function expire(): bool
	{
		$this->expiration = now();
		return $this->save();
	}

}