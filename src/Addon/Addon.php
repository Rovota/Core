<?php

/**
 * @author      Software Department <developers@rovota.com>
 * @copyright   Copyright (c), Rovota
 * @license     MIT
 */

namespace Rovota\Core\Addon;

use Rovota\Core\Addon\Enums\AddonChannel;
use Rovota\Core\Addon\Enums\AddonType;
use Rovota\Core\Database\Model;
use Rovota\Core\Support\Enums\Status;
use Rovota\Core\Support\Moment;
use Rovota\Core\Support\Version;

/**
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string|null $description
 * @property string $support
 * @property string $vendor
 * @property AddonChannel $channel
 * @property array|null $domain_list
 * @property array|null $dependency_list
 * @property string|null $license
 * @property Version $version
 * @property bool $auto_update
 * @property AddonType $type
 * @property Status $status
 * @property Moment|null $created
 * @property Moment|null $edited
 * @property Moment|null $deleted
 */
class Addon extends Model
{

	protected string|null $table = 'addons';

	protected array $attributes = [
		'channel' => AddonChannel::Stable,
		'auto_update' => false,
		'status' => Status::Disabled,
	];

	protected array $casts = [
		'channel' => ['enum', AddonChannel::class],
		'domain_list' => 'array',
		'dependency_list' => 'array',
		'version' => ['object', Version::class],
		'auto_update' => 'bool',
		'type' => ['enum', AddonType::class],
		'status' => ['enum', Status::class],
	];

	protected array $guarded = ['id', 'created', 'edited', 'deleted'];

	protected bool $enable_composites = false;

	// -----------------

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
		$this->registerRoutes();
	}

	// -----------------

	/**
	 * Called when each add-on is being loaded.
	 */
	protected function registerRoutes(): void
	{

	}

	// -----------------

	// TODO: uninstall & table methods

	// -----------------

	/**
	 * Called when all add-ons have been successfully loaded.
	 */
	public function eventAllAddonsLoaded(): void
	{

	}

}