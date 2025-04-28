<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Core\Auth;

use Rovota\Core\Auth\Enums\SuspensionType;
use Rovota\Core\Auth\Interfaces\Identity;
use Rovota\Core\Database\Model;
use Rovota\Core\Support\Moment;
use function now;

/**
 * @property int $id
 * @property int $user_id
 * @property string $code
 * @property string $reason
 * @property Moment|null $expiration
 * @property SuspensionType $type
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class Suspension extends Model
{

	protected string|null $table = 'suspensions';

	protected array $attributes = [
		'type' => SuspensionType::Automatic,
	];

	protected array $casts = [
		'expiration' => 'moment',
		'type' => ['enum', SuspensionType::class],
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	protected bool $enable_composites = false;

	// -----------------

	public static function createUsing(Identity $identity, array $attributes = []): static
	{
		$attributes = array_merge([
			'user_id' => $identity->getId(),
			'code' => 'MISC',
			'reason' => 'No reason has been provided.',
			'expiration' => now()->addDays(7),
			'type' => 'automatic',
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