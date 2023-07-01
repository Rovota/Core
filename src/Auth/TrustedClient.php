<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Auth;

use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Database\Model;
use Rovota\Core\Http\RequestManager;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $label
 * @property string $ip
 * @property string $hash
 * @property Moment $expiration
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class TrustedClient extends Model
{

	protected string|null $table = 'trusted_clients';

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
			'name' => RequestManager::getRequest()->device(),
			'label' => RequestManager::getRequest()->client() ?? 'Unknown',
			'ip' => RequestManager::getRequest()->ip(),
			'hash' => Str::random(80),
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